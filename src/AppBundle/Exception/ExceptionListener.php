<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionListener
{
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if (!$exception instanceof \Google_Exception) {
            return;
        }

        $jsonException = new JsonHttpException($exception->getCode(), $exception->getMessage());

        $event->setException($jsonException);
    }
}
