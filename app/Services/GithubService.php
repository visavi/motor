<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use Symfony\Contracts\Cache\CacheInterface;
use Throwable;

class GithubService
{
    private Client $client;

    public function __construct(private readonly CacheInterface $cache)
    {
        $this->client = new Client([
            'base_uri' => 'https://api.github.com/repos/visavi/motor/',
            'timeout'  => 3.0,
        ]);
    }

    /**
     * Send request
     *
     * @param string $uri
     *
     * @return array
     */
    public function sendRequest(string $uri): array
    {
        try {
            return $this->cache->get($uri, function () use ($uri): array {
                $params = [
                    'query' => [
                        'per_page' => 10,
                    ]
                ];

                $response = $this->client->get($uri, $params);
                $content = $response->getBody()->getContents();

                return json_decode($content, true, 512, JSON_THROW_ON_ERROR);
            });
        } catch (Throwable) {
            // nothing
        }

        return [];
    }

    /**
     * Get releases
     *
     * @return array
     */
    public function getReleases(): array
    {
        return $this->sendRequest('releases');
    }

    /**
     * Get last release
     *
     * @return array
     */
    public function getLastRelease(): array
    {
        $releases = $this->sendRequest('releases');

        return $releases[0] ?? [];
    }

    public function getCommits(): array
    {
        return $this->sendRequest('commits');
    }
}
