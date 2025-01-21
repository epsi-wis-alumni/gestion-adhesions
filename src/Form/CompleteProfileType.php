<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompleteProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : jeanmarc@contact.com',
                ],
                'row_attr' => ['class' => 'mb-3'],
            ])
            ->add('firstname', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Jean-Marc',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Prénom'
            ])
            ->add('lastname', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Dupont',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Nom',
            ])
            ->add('company', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : EPSI-WIS Alumni',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Entreprise',
            ])
            ->add('jobTitle', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Data Scientist',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Poste',
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
