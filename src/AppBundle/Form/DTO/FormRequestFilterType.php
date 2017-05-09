<?php

namespace AppBundle\Form\DTO;

use AppBundle\Entity\DTO\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormRequestFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
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
                    'Personal Day' => 'Personal Day',
                    'Overnight Guest' => 'Overnight Guest',
                    'Sick Day' => 'Sick Day',
                ],
            ])
            ->add('decision', ChoiceType::class, [
                'attr' => ['class' => 'input-sm form-control'],
                'choices' => [
                    'All' => 'All',
                    'Approved' => 'approved',
                    'Rejected' => 'rejected',
                    'No Decision' => 'pending',
                ],
            ])
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'csrf_protection' => false,
        ]);
    }
}
