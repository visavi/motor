<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Services\Setting;
use App\Services\View;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpException;
use Slim\Handlers\ErrorHandler as SlimErrorHandler;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\Util\Misc;

class HttpErrorHandler extends SlimErrorHandler
{
    protected ContainerInterface $container;

    /**
     * @inheritdoc
     */
    protected function respond(): Response
    {
        $response = $this->responseFactory->createResponse();

        if (
            $this->exception instanceof HttpException
            || ! $this->container->get(Setting::class)->get('debug')
        ) {
            $code = $this->statusCode;

            if (! $this->container->get(View::class)->exists('errors/' . $code)) {
                $code = 500;
            }

            $response = $this->container->get(View::class)->render(
                $response,
                'errors/' . $code,
                ['message' => $this->exception->getMessage()]
            );

            return $response->withStatus($code);
        }

        if (
            class_exists(Run::class)
            && $this->container->get(Setting::class)->get('debug')
        ) {
            $handler = Misc::isAjaxRequest() ?
                new JsonResponseHandler() :
                new PrettyPageHandler();

            $whoops = new Run();
            $whoops->pushHandler($handler);

            $response->getBody()->write($whoops->handleException($this->exception));
        }

        return $response;

    }

    /**
     * Set container
     *
     * @param ContainerInterface $container
     * @return void
     */
    public function setContainer(ContainerInterface $container): void
    {
        $this->container = $container;
    }
}
