<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Survey\SurveyTypeSection;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SurveyTypeSectionNormalizer extends ObjectNormalizer
{
    /**
     * SurveyNormalizer constructor.
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
        return $data instanceof SurveyTypeSection;
    }
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        /** @var SurveyTypeSection $typeSection */
        $typeSection = &$object;

        return $this->serializer->normalize(new \ArrayObject([
            'id' => $typeSection->getId(),
            'name' => $typeSection->getName(),
            'description' => $typeSection->getDescription(),
            'orderNumber' => $typeSection->getOrderNumber(),
            'questions' => $typeSection->getQuestions(),
        ]), $format, $context);
    }
}
