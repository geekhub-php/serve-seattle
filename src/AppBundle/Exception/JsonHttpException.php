<?php

namespace AppBundle\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class JsonHttpException extends HttpException
{
    /**
     * @var array
     */
    private $data;

    /**
     * @param int    $statusCode
     * @param string $message
     * @param mixed  $data
     */
    public function __construct($statusCode, $message = null, $data = [])
    {
        parent::__construct($statusCode, $message);

        $this->setData($data);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }
}
