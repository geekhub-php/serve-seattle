<?php

namespace AppBundle\Listener;

use AppBundle\Exception\JsonHttpException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class JsonExceptionListener
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        if ($exception instanceof JsonHttpException) {
            $errorData = [
                'error' => [
                    'code' => $exception->getStatusCode(),
                    'message' => $exception->getMessage(),
                ],
            ];
            if (($data = $exception->getData())) {
                $errorData['error']['fields'] = $data;
            }
            $response = new JsonResponse($errorData);
            $event->setResponse($response);
            $this->logger->error($exception->getMessage(), $errorData);
        }
    }
}
