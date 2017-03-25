<?php

namespace AppBundle\Services;

use AppBundle\Entity\Survey\Survey;
use AppBundle\Entity\Survey\SurveyAnswer;
use AppBundle\Entity\Survey\SurveyQuestion;
use Symfony\Component\Form\Exception\LogicException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SurveyNormalizer implements DenormalizerInterface
{
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $em)
    {
        $this->em = $em;
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

        return $this->serializer->normalize(new \ArrayObject([
            'id' => $survey->getId(),
            'type' => $survey->getType(),
            'status' => $survey->getStatus(),
            'answers' => $survey->getAnswers(),
            'createdAt' => $survey->getCreatedAt(),
            'updatedAt' => $survey->getUpdatedAt()
        ]), $format, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function denormalize($data, $class, $format = null, array $context = array())
    {
        if (isset($context['object_to_populate'])) {
            $survey = $context['object_to_populate'];
            $survey->setStatus('submited');
            if (isset($data['answers']) && is_array($data['answers'])) {
                foreach ($survey->getQuestions() as $question) {
                    $questionsId[] = $question->getId();
                }
                foreach ($data['answers'] as $answer) {
                    $dataId[] = $answer['question']['id'];
                }
                if (array_diff($questionsId, $dataId)) {
                    return null;
                }
                foreach ($data['answers'] as $answer) {
                    $newAnswer = new SurveyAnswer();
                    $question = $this->em->getRepository(SurveyQuestion::class)
                                ->find($answer['question']['id']);
                    if ($question->getVariants()) {
                        if (!in_array($answer['content'], $question->getVariants())) {
                            return null;
                        }
                    }
                    $content = $answer['content'];
                    $newAnswer->setSurvey($survey);
                    $newAnswer->setQuestion($question);
                    $newAnswer->setContent($content);
                    $this->em->persist($newAnswer);
                }
                $this->em->flush();
            }

            return $survey;
        }
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
