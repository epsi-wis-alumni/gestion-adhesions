<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminUserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : jeanmarc@contact.com',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Email',
            ])
            ->add('firstname', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Jean-Marc',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Dupont',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('username', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : JM Dupont',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Surnom',
                'required' => false,
            ])
            ->add('company', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : EPSI-WIS Alumni',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Entreprise',
                'required' => false,
            ])
            ->add('jobTitle', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Secrétaire',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Poste',
                'required' => false,
            ])
            ->add('avatar', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : https://lh3.googleusercontent.com/a/ACg8ocK26dvnht8mJB2FE',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Avatar',
                'required' => false,
            ])
            ->add('googleId', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : 1546781203241548749158',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Google Id',
                'required' => false,
            ])
            ->add('microsoftId', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : 550e8400-e29b-41d4-a716-446655440000',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Microsoft Id',
                'required' => false,
            ])
            ->add('githubId', TextType::class, [
                'attr'=> [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : 12345678',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Github Id',
                'required' => false,
            ])
            // ->add('linkedinId', TextType::class, [
            //     'attr'=> [
            //         'class' => 'form-control',
            //         'placeholder' => 'Ex : AAbc123DEfg45HIJklmN',
            //     ],
            //     'row_attr' => [
            //         'class' => 'mb-3',
            //     ],
            //     'label' => 'LinkedIn Id',
            //     'required' => false,
            // ])
            ->add('isAdmin', CheckboxType::class, [
                'attr'=> [
                    'class' => 'form-check-input',
                    'placeholder' => 'Ex : 12345678',
                ],
                'row_attr' => [
                    'class' => 'mb-3 form-check form-switch ps-3',
                ],
                'label' => 'Administrateur',
                'label_attr' => [
                    'class' => 'form-check-label',
                ],
                'mapped' => false,
                'required' => false,
                'data' => in_array('ROLE_ADMIN', $options['data']->getRoles(), true), // Définir la valeur initiale
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
