<?php

declare(strict_types=1);

namespace App\Services;

use Closure;
use Psr\Http\Message\ServerRequestInterface as Request;
use RuntimeException;

class CloudFlare
{
    private array $checkedIps = [];

    /**
     * List of IP's used by CloudFlare.
     * @const array
     */
    protected const IPS = [
        '103.21.244.0/22',
        '103.22.200.0/22',
        '103.31.4.0/22',
        '104.16.0.0/13',
        '104.24.0.0/14',
        '108.162.192.0/18',
        '131.0.72.0/22',
        '141.101.64.0/18',
        '162.158.0.0/15',
        '172.64.0.0/13',
        '173.245.48.0/20',
        '188.114.96.0/20',
        '190.93.240.0/20',
        '197.234.240.0/22',
        '198.41.128.0/17',
        '2400:cb00::/32',
        '2405:8100::/32',
        '2405:b500::/32',
        '2606:4700::/32',
        '2803:f800::/32',
        '2c0f:f248::/32',
        '2a06:98c0::/29',
    ];

    /**
     * @var Request
     */
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Checks if current request is coming from CloudFlare servers.
     *
     * @return bool
     */
    public function isTrustedRequest(): bool
    {
        return $this->checkIp($this->request->getAttribute('ip'), static::IPS);
    }

    /**
     * Executes a callback on a trusted request.
     *
     * @param  Closure $callback
     *
     * @return mixed
     */
    public function onTrustedRequest(Closure $callback)
    {
        if ($this->isTrustedRequest()) {
            return $callback();
        }
    }

    /**
     * Determines "the real" IP address from the current request.
     *
     * @return string
     */
    public function ip(): string
    {
        return $this->onTrustedRequest(function () {
            return filter_var($this->request->getHeaderLine('CF_CONNECTING_IP'), FILTER_VALIDATE_IP);
        }) ?: $this->request->getAttribute('ip');
    }

    /**
     * Determines country from the current request.
     *
     * @return string
     */
    public function country(): string
    {
        return $this->onTrustedRequest(function () {
            return $this->request->getHeaderLine('CF_IPCOUNTRY');
        }) ?: '';
    }

    /**
     * Checks if an IPv4 or IPv6 address is contained in the list of given IPs or subnets.
     *
     * @param string|array $ips List of IPs or subnets (can be a string if only a single one)
     */
    public function checkIp(string $requestIp, string|array $ips): bool
    {
        if (!is_array($ips)) {
            $ips = [$ips];
        }

        $method = substr_count($requestIp, ':') > 1 ? 'checkIp6' : 'checkIp4';

        foreach ($ips as $ip) {
            if ($this->$method($requestIp, $ip)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compares two IPv4 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @param string $ip IPv4 address or subnet in CIDR notation
     *
     * @return bool Whether the request IP matches the IP, or whether the request IP is within the CIDR subnet
     */
    public function checkIp4(string $requestIp, string $ip): bool
    {
        $cacheKey = $requestIp.'-'.$ip;
        if (isset($this->checkedIps[$cacheKey])) {
            return $this->checkedIps[$cacheKey];
        }

        if (!filter_var($requestIp, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $this->checkedIps[$cacheKey] = false;
        }

        if (str_contains($ip, '/')) {
            [$address, $netmask] = explode('/', $ip, 2);

            if ('0' === $netmask) {
                return $this->checkedIps[$cacheKey] = filter_var($address, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
            }

            if ($netmask < 0 || $netmask > 32) {
                return $this->checkedIps[$cacheKey] = false;
            }
        } else {
            $address = $ip;
            $netmask = 32;
        }

        if (false === ip2long($address)) {
            return $this->checkedIps[$cacheKey] = false;
        }

        return $this->checkedIps[$cacheKey] = 0 === substr_compare(sprintf('%032b', ip2long($requestIp)), sprintf('%032b', ip2long($address)), 0, (int) $netmask);
    }

    /**
     * Compares two IPv6 addresses.
     * In case a subnet is given, it checks if it contains the request IP.
     *
     * @author David Soria Parra <dsp at php dot net>
     *
     * @see https://github.com/dsp/v6tools
     *
     * @param string $ip IPv6 address or subnet in CIDR notation
     *
     * @throws RuntimeException When IPV6 support is not enabled
     */
    public function checkIp6(string $requestIp, string $ip): bool
    {
        $cacheKey = $requestIp.'-'.$ip;
        if (isset($this->checkedIps[$cacheKey])) {
            return $this->checkedIps[$cacheKey];
        }

        if (!((extension_loaded('sockets') && defined('AF_INET6')) || @inet_pton('::1'))) {
            throw new RuntimeException('Unable to check Ipv6. Check that PHP was not compiled with option "disable-ipv6".');
        }

        if (str_contains($ip, '/')) {
            [$address, $netmask] = explode('/', $ip, 2);

            if ('0' === $netmask) {
                return (bool) unpack('n*', @inet_pton($address));
            }

            if ($netmask < 1 || $netmask > 128) {
                return $this->checkedIps[$cacheKey] = false;
            }
        } else {
            $address = $ip;
            $netmask = 128;
        }

        $bytesAddr = unpack('n*', @inet_pton($address));
        $bytesTest = unpack('n*', @inet_pton($requestIp));

        if (!$bytesAddr || !$bytesTest) {
            return $this->checkedIps[$cacheKey] = false;
        }

        for ($i = 1, $ceil = ceil($netmask / 16); $i <= $ceil; ++$i) {
            $left = $netmask - 16 * ($i - 1);
            $left = ($left <= 16) ? $left : 16;
            $mask = ~(0xFFFF >> $left) & 0xFFFF;
            if (($bytesAddr[$i] & $mask) != ($bytesTest[$i] & $mask)) {
                return $this->checkedIps[$cacheKey] = false;
            }
        }

        return $this->checkedIps[$cacheKey] = true;
    }
}
