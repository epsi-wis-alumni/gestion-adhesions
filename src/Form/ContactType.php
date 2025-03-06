<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'choices' => [
                    'M.' => 'M',
                    'Mme' => 'Mme',
                    'Autre' => 'Autre',
                ],
                'attr' => [
                    'class' => 'd-flex gap-4 w-100',
                ],
                'expanded' => true,
                'multiple' => false,
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Civilité',
            ])
            ->add('firstname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'attr' => [
                    'placeholder' => 'Nom',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Nom',
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'placeholder' => 'E-mail',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'E-mail',
            ])
            ->add('tel', TelType::class, [
                'attr' => [
                    'placeholder' => 'Téléphone',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Téléphone',
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Ici votre message...',
                    'rows' => '5',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Message',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
        ]);
    }
}
