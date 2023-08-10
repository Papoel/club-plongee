<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ErrorController extends AbstractController
{
    public function show(FlattenException $exception, Environment $twig): Response
    {
        $template = 'bundles/TwigBundle/Exception/error{$exception->getStatusCode()}.html.twig';

        if (!$twig->getLoader()->exists($template)) {
            $template = 'bundles/TwigBundle/Exception/error.html.twig';
        }

        return $this->render($template, [
            'status_code' => $exception->getStatusCode(),
            'status_text' => Response::$statusTexts[$exception->getStatusCode()],
            'exception' => $exception,
        ]);
    }
}
