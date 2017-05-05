<?php

namespace AppBundle\Serializer;

use AppBundle\Entity\Survey\Survey;
use AppBundle\Entity\Survey\SurveyAnswer;
use AppBundle\Entity\Survey\SurveyQuestion;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\Serializer\Exception\LogicException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactoryInterface;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class SurveyAnswerNormalizer extends ObjectNormalizer
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * SurveyAnswerNormalizer constructor.
     *
     * @param ClassMetadataFactoryInterface|null  $classMDF
     * @param NameConverterInterface|null         $nameCv
     * @param PropertyAccessorInterface|null      $propAs
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
        return $data instanceof SurveyAnswer;
    }
    /**
     * {@inheritdoc}
     */
    public function normalize($object, $format = null, array $context = [])
    {
        if (!$this->serializer instanceof NormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        /** @var SurveyAnswer $answer */
        $answer = &$object;

        return $this->serializer->normalize(new \ArrayObject([
            'questionId' => $answer->getQuestion()->getId(),
            'content' => $answer->getContent(),
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
        /** @var Survey $survey */
        $survey = $context[ObjectNormalizer::OBJECT_TO_POPULATE];

        if (!array_key_exists('questionId', $data) || !array_key_exists('content', $data)) {
            throw new LogicException('Wrong json consruction');
        }

        $newAnswer = new SurveyAnswer();
        $question = $this->doctrine->getManager()->getRepository(SurveyQuestion::class)
            ->find($data['questionId']);
        if (!in_array($question, $survey->getQuestions())) {
            throw new LogicException('Wrong question id');
        }
        if ($question->getVariants() && $question->getId() != '60') {
            if (!in_array($data['content'], $question->getVariants())) {
                throw new LogicException('Wrong variants');
            }
        }
        $newAnswer->setQuestion($question);
        $newAnswer->setContent($data['content']);

        return $newAnswer;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null)
    {
        if ($type != SurveyAnswer::class) {
            return false;
        }

        return true;
    }
}
