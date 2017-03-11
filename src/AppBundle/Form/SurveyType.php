<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

/**
 * Class SurveyType.

 */
class SurveyType extends AbstractType
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
            ->add('user', EntityType::class, array(
                'class' => 'AppBundle:User',
                'label' => 'Choose intern',
                'choice_label' => 'firstName',
            ))
            ->add('created_at', DateTimeType::class, array(
                'disabled' => true,
            ))
            ->add('updated_at', DateTimeType::class, array(
                'disabled' => true,
            ))
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $survey = $event->getData();
            $form = $event->getForm();
            if ($survey->getUser() === null) {
                $form->remove('created_at');
                $form->remove('updated_at');
            }
            if ($survey->getUser() !== null) {
                $form->remove('user');
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Survey',
        ));
    }
}
