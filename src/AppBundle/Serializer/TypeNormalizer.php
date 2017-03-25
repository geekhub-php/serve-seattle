<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Survey\SurveyType;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TypeNormalizer extends ObjectNormalizer
{
    /**
     * SurveyNormalizer constructor.
     *
     * @param ClassMetadataFactoryInterface|null  $classMDF
     * @param NameConverterInterface|null         $nameCv
     * @param PropertyAccessorInterface|null      $propertyAs
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
        return $data instanceof SurveyType;
    }
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        /** @var SurveyType $type */
        $type = &$object;
        if (isset($context[ObjectNormalizer::GROUPS])) {
            if ($context[ObjectNormalizer::GROUPS][0] == 'list') {
                return $this->serializer->normalize(new \ArrayObject([
                    'name' => $type->getName(),
                ]), $format, $context);
            }
        }

        return $this->serializer->normalize(new \ArrayObject([
            'id' => $type->getId(),
            'name' => $type->getName(),
            'description' => $type->getDescription(),
            'section' => $type->getSections(),
        ]), $format, $context);
    }
}
