<?php

declare(strict_types=1);

namespace App\Services;

use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface;

class View
{
    private Engine $engine;

    public function __construct(string $directory, string $fileExtension = 'php')
    {
        $this->engine = new Engine($directory, $fileExtension);
    }

    /**
     * @param ResponseInterface $response
     * @param string            $name
     * @param array             $data
     *
     * @return ResponseInterface
     */
    public function render(ResponseInterface $response, string $name, array $data = []): ResponseInterface
    {
        $render = $this->engine->render($name, $data);

        $response->getBody()->write($render);

        return $response;
    }

    /**
     * @return Engine
     */
    public function getEngine(): Engine
    {
        return $this->engine;
    }
}
