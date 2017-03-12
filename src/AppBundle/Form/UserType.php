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
 * Class UserType.
 */
class UserType extends AbstractType
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
            ->add(
                'firstName', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'firstName',
                    'class' => 'form-control',
                ),
                'label' => false,
                )
            )
            ->add(
                'lastName', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'lastName',
                    'class' => 'form-control',
                ),
                'label' => false,
                )
            )
            ->add(
                'email', EmailType::class, array(
                'attr' => array(
                    'placeholder' => 'E-mail',
                    'class' => 'form-control',
                ),
                'label' => false,
                )
            )
            ->add(
                'userName', TextType::class, array(
                'attr' => array(
                    'placeholder' => 'UserName',
                    'class' => 'form-control',
                ),
                'label' => false,
                )
            )
            ->add(
                'password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => array(
                        'attr' => array(
                            'placeholder' => 'Password',
                            'class' => 'form-control',
                        ),
                        'label' => false,
                    ),
                    'second_options' => array(
                        'attr' => array(
                            'placeholder' => 'Repeat password',
                            'class' => 'form-control',
                        ),
                        'label' => false,
                    ),
                    'required' => false,
                )
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\User',
            )
        );
    }
}
