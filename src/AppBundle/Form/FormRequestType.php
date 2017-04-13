<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\FormRequest;

/**
 * Class UserType.
 */
class FormRequestType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     * @SuppressWarnings("UnusedFormalParameter")
     * After add new field in UserType need create
     * offsetUnset() method from this field in Security controller
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'approved',
                    'rejected',
                ],
                'choice_attr' => function($val, $key, $index) {
                    // adds a class like attending_yes, attending_no, etc
                    return ['hidden' => true];
                },
                'label' => false,
                'multiple' => false,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormRequest::class,
        ]);
    }
}
