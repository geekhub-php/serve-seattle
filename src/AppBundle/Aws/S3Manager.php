<?php

namespace AppBundle\Aws;

use AppBundle\Entity\S3\Image;
use Aws\S3\S3Client;
use Psr\Log\LoggerInterface;

class S3Manager
{
    /**
     * @var S3Client
     */
    private $s3;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $prefix;

    public function __construct(S3Client $s3, $bucket, LoggerInterface $logger, $prefix)
    {
        $this->s3 = $s3;
        $this->bucket = $bucket;
        $this->logger = $logger;
        $this->prefix = $prefix;
    }

    public function upload(Image $image)
    {
        if (!$image->getContent()) {
            return $image;
        }
        $image->setS3key(sprintf('%s/%s', $this->prefix, $image->getS3key()));
        try {
            $result = $this->s3->putObject([
                'Bucket' => $this->bucket,
                'Key' => $image->getS3key(),
                'Body' => $image->getContent(),
                'ACL' => 'public-read-write',
                'ContentType' => $image->getContentType(),
            ]);

            $image->setUrl($result->get('ObjectURL'));
        } catch (\Exception $e) {
            $this->logger->error(
                'error write image to aws bucket',
                [
                    'bucket' => $this->bucket,
                    'key' => $image->getS3key(),
                    'file' => $image->getNameFile(),
                    'message' => $e->getMessage(),
                ]
            );
        }

        return $image;
    }

    public function removeImage($key)
    {
        try {
            $this->s3->deleteMatchingObjects($this->bucket, $key);
        } catch (\Exception $e) {
            $this->logger->error(
                'error delete image from aws bucket',
                [
                    'bucket' => $this->bucket,
                    'key' => $key,
                    'message' => $e->getMessage(),
                ]
            );
        }
    }
}
