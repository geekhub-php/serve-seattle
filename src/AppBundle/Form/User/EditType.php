<?php

namespace AppBundle\Form\User;

use AppBundle\Entity\S3\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use AppBundle\Entity\User;

/**
 * Class UserType.

 */
class EditType extends AbstractType
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
            ->add('firstName', TextType::class, [
                'attr' => [
                    'placeholder' => 'First Name',
                    'class' => 'form-control',
                ],
                'label' => false,
            ])
            ->add('lastName', TextType::class, [
                'attr' => [
                    'placeholder' => 'Last Name',
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
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
                $user = $event->getData();

                if ($user instanceof User && $user->getImage()) {
                    /** @var Image $image */
                    $image = $user->getImage();
                    $form = $event->getForm();
                    $form->add('image', TextType::class, [
                        'attr' => [
                        'placeholder' => 'image',
                        'class' => 'form-control',
                    ],
                        'label' => false,
                        'required' => false,
                        'disabled' => true,
                        'data' => $image->getUrl()
                    ]);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'validation_groups' => ['registration', 'edit'],
        ]);
    }
}
