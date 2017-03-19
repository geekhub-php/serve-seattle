<?php

namespace AppBundle\Form\DTO;

use AppBundle\Entity\DTO\Filter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormRequestFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', TextType::class, [
                'attr' => ['class' =>  'input-sm form-control'],
                'required' => false,
            ])
            ->add('end', TextType::class, [
                'attr' => ['class' =>  'input-sm form-control'],
                'required' => false,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'All' => 'All' ,
                    'Personal Day' => 'Personal Day',
                    'Overnight Guest' => 'Overnight Guest',
                    'Sick Day' => 'Sick Day',
                ],
            ])
            ->add('decision', ChoiceType::class, [
                'choices' => [
                    'All' => 'All' ,
                    'Approved' => 'approved',
                    'Rejected' => 'rejected',
                    'No Decision' => 'pending',
                ],
            ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Filter::class,
            'method' => Request::METHOD_GET,
            'csrf_protection' => false,
        ]);
    }
}
