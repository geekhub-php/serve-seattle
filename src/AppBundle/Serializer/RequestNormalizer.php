<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\FormRequest;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class RequestNormalizer extends ObjectNormalizer
{
    /**
     * RequestNormalizer constructor.
     *
     * @param ClassMetadataFactoryInterface|null  $classMDF
     * @param NameConverterInterface|null         $nameCv
     * @param PropertyAccessorInterface|null      $propAs
     * @param PropertyTypeExtractorInterface|null $propTE
     */
    public function __construct($classMDF, $nameCv, $propAs, $propTE)
    {
        parent::__construct($classMDF, $nameCv, $propAs, $propTE);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof FormRequest;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        /** @var FormRequest $request */
        $request = &$object;

        return $this->serializer->normalize(new \ArrayObject([
            'id' => $request->getId(),
            'type' => $request->getType(),
            'status' => $request->getStatus(),
            'reason' => $request->getReason(),
            'date' => $request->getDate(),
            'createdAt' => $request->getCreatedAt(),
            'updatedAt' => $request->getUpdatedAt(),
        ]), $format, $context);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param mixed  $data
     * @param string $class
     * @param null   $format
     * @param array  $context
     *
     * @return FormRequest
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!$this->serializer instanceof DenormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        /** @var FormRequest $request */
        $request = $context[ObjectNormalizer::OBJECT_TO_POPULATE];

        $request->setDate($data['date']);
        if (isset($data['reason'])) {
            $request->setReason($data['reason']);
        }

        return $request;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type != FormRequest::class) {
            return false;
        }

        return true;
    }
}
