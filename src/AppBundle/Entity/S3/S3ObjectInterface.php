<?php

namespace AppBundle\Entity\S3;

interface S3ObjectInterface
{
    public function getS3Key();

    public function setS3Key($key);

    public function getContentType();

    public function getUrl();
}
