<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

/**
 * Class AdminType.

 */
class AdminType extends AbstractType
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
            ->add('userName', TextType::class, [
                'attr' => [
                    'placeholder' => 'UserName',
                    'class' => 'form-control',
                ],
                'label' => false,
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'E-mail',
                    'class' => 'form-control',
                ],
                'label' => false,
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Password',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Repeat password',
                        'class' => 'form-control',
                    ],
                    'label' => false,
                ],
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Admin',
        ]);
    }
}
