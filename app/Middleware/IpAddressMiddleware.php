<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface as Middleware;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

final class IpAddressMiddleware implements Middleware
{
    public function process(
        Request $request,
        RequestHandler $handler,
    ): Response {

        $ipAddress = $this->determineClientIpAddress($request);

        $request = $request->withAttribute('ip', $ipAddress);

        return $handler->handle($request);
    }

    /**
     * Find out the client's IP address from the headers available to us
     *
     * @param Request $request PSR-7 Request
     *
     * @return string
     */
    protected function determineClientIpAddress(Request $request): string
    {
        $ipAddress = '';

        $serverParams = $request->getServerParams();
        if (isset($serverParams['REMOTE_ADDR'])) {
            $remoteAddr = $this->extractIpAddress($serverParams['REMOTE_ADDR']);
            if ($this->isValidIpAddress($remoteAddr)) {
                $ipAddress = $remoteAddr;
            }
        }

        return $ipAddress;
    }

    /**
     * Remove port from IPV4 address if it exists
     *
     * Note: leaves IPV6 addresses alone
     *
     * @param  string $ipAddress
     * @return string
     */
    protected function extractIpAddress(string $ipAddress): string
    {
        $parts = explode(':', $ipAddress);

        if (count($parts) === 2) {
            if (filter_var($parts[0], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false) {
                return $parts[0];
            }
        }

        return $ipAddress;
    }

    /**
     * Check that a given string is a valid IP address
     *
     * @param  string  $ip
     *
     * @return bool
     */
    protected function isValidIpAddress(string $ip): bool
    {
        $flags = FILTER_FLAG_IPV4 | FILTER_FLAG_IPV6;

        return filter_var($ip, FILTER_VALIDATE_IP, $flags) !== false;
    }
}
