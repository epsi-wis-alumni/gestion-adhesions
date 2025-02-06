<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminEventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Soirée d\'ouverture',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Titre',
            ])
            ->add('place', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : 349 rue de la Cavalade Montpellier 34000',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Lieu',
            ])
            ->add('startAt', null, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Date de début',
            ])
            ->add('endAt', null, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Date de fin',
            ])
            ->add('private', CheckboxType::class, [
                'label' => 'Privé',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'required' => false,
            ])
            ->add('notifyByEmail', CheckboxType::class, [
                'label' => 'Notifier par email',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'required' => false,
                'mapped' => false,
                'row_attr' => ['class' => 'mb-3'],
                'data' => true,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}
