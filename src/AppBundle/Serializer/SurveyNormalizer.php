<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Survey\Survey;
use AppBundle\Entity\Survey\SurveyAnswer;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SurveyNormalizer extends ObjectNormalizer
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * SurveyNormalizer constructor.
     *
     * @param ClassMetadataFactoryInterface|null  $classMDF
     * @param NameConverterInterface|null         $nameCv
     * @param PropertyAccessorInterface|null      $propertyAs
     * @param PropertyTypeExtractorInterface|null $propTE
     * @param Registry                            $doctrine
     */
    public function __construct($classMDF, $nameCv, $propAs, $propTE, Registry $doctrine)
    {
        parent::__construct($classMDF, $nameCv, $propAs, $propTE);
        $this->doctrine = $doctrine;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof Survey;
    }
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        /** @var Survey $survey */
        $survey = &$object;
        if (isset($context[ObjectNormalizer::GROUPS])) {
            if ($context[ObjectNormalizer::GROUPS][0] == 'list') {
                return $this->serializer->normalize(new \ArrayObject([
                    'id' => $survey->getId(),
                    'type' => $survey->getType(),
                    'status' => $survey->getStatus(),
                    'createdAt' => $survey->getCreatedAt(),
                    'updatedAt' => $survey->getUpdatedAt(),
                ]), $format, $context);
            }
        }

        return $this->serializer->normalize(new \ArrayObject([
            'id' => $survey->getId(),
            'type' => $survey->getType(),
            'status' => $survey->getStatus(),
            'answers' => $survey->getAnswers(),
            'createdAt' => $survey->getCreatedAt(),
            'updatedAt' => $survey->getUpdatedAt(),
        ]), $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!$this->serializer instanceof DenormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        if (!isset($context[ObjectNormalizer::OBJECT_TO_POPULATE])) {
            throw new LogicException('Not found object_to_populate');
        }
        /** @var Survey $survey */
        $survey = $context[ObjectNormalizer::OBJECT_TO_POPULATE];

        if (!array_key_exists('answers', $data)) {
            throw new LogicException('Wrong json consruction');
        }
        foreach ($data['answers'] as $item) {
            $answer = $this->serializer->denormalize($item, SurveyAnswer::class, $format, $context);
            $answer->setSurvey($survey);
            $answers[] = $answer;
            $this->doctrine->getManager()->persist($answer);
        }
        foreach ($answers as $answer) {
            $questions[] = $answer->getQuestion()->getId();
        }
        foreach ($survey->getQuestions() as $question) {
            $surveyQuestions[] = $question->getId();
        }
        if ($surveyQuestions !== $questions) {
            throw new LogicException('Wrong json content');
        }
        $survey->setStatus('submited');
        $this->doctrine->getManager()->persist($survey);
        $this->doctrine->getManager()->flush();

        return $survey;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type != Survey::class) {
            return false;
        }

        return true;
    }
}
