<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use Shieldon\SimpleCache\Cache;
use Throwable;

class GithubService
{
    private Client $client;

    public function __construct(private Cache $cache)
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
            $content = $this->cache->get($uri);
            if ($content) {
                return json_decode($content, true);
            }

            $params = [
                'query' => [
                    'per_page' => 10,
                ]
            ];

            $response = $this->client->get($uri, $params);
            $content = $response->getBody()->getContents();

            if ($content) {
                $this->cache->set($uri, $content, 3600);

                return json_decode($content, true);
            }
        } catch (Throwable $e) {
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
