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
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (!$this->serializer instanceof DenormalizerInterface) {
            throw new LogicException('Cannot normalize attributes because injected serializer is not a normalizer');
        }
        /** @var Survey $survey */
        $survey = $context[ObjectNormalizer::OBJECT_TO_POPULATE];

        $fields = $this->getAllowedAttributes(SurveyAnswer::class, $context);
        foreach ($fields as $field) {
            if (!in_array($field->getName(), array_keys($data)) || !array_key_exists('id', $data['question'])) {
                throw new LogicException('Wrong json consruction');
            }
        }

        $newAnswer = new SurveyAnswer();
        $question = $this->doctrine->getManager()->getRepository(SurveyQuestion::class)
            ->find($data['question']['id']);
        if (!in_array($question, $survey->getQuestions())) {
            throw new LogicException('Wrong question id');
        }
        if ($question->getVariants()) {
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
