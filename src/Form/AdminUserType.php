<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AdminUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Adresse email',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Email',
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Prénom',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Prénom',
                'required' => false,
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Nom',
                'required' => false,
            ])
            ->add('company', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Entreprise',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Entreprise',
                'required' => false,
            ])
            ->add('jobTitle', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Poste occupé',
                ],
                'row_attr' => [
                    'class' => 'mb-3',
                ],
                'label' => 'Poste occupé',
                'required' => false,
            ])
            ->add('isAdmin', CheckboxType::class, [
                'label' => 'Administrateur',
                'label_attr' => [
                    'class' => 'checkbox-switch',
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
