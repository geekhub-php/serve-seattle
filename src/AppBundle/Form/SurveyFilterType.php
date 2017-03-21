<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
            ->add('start', DateType::class, array(
                'widget' => 'choice',
                'input' => 'datetime',
            ))
            ->add('end', DateType::class, array(
                'widget' => 'choice',
                'input' => 'datetime',
            ))
            ->add('type', EntityType::class, array(
                'class' => 'AppBundle\Entity\Survey\SurveyType',
                'label' => 'Survey type',
                'choice_label' => 'name',
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\DTO\SurveyFilter',
        ));
    }
}
