<?php

namespace AppBundle\Form\DTO;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

/**
 * Class SurveyFilterType.

 */
class SurveyFilterType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class, [
                'attr' => ['class' => 'input-sm form-control'],
                'required' => false,
                'widget' => 'single_text',
                'format' => 'MM/dd/yyyy',
            ])
            ->add('end', DateTimeType::class, [
                'attr' => ['class' => 'input-sm form-control'],
                'required' => false,
                'widget' => 'single_text',
                'format' => 'MM/dd/yyyy',
            ])
            ->add('type', ChoiceType::class, [
                'attr' => ['class' => 'input-sm form-control'],
                'choices' => [
                    'All' => 'All',
                    'Internship Survey' => 'internship',
                    'Speaker Survey' => 'speaker',
                    'Exit Survey' => 'exit',
                    'Supervisor Survey' => 'supervisor',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DTO\Filter',
        ));
    }
}
