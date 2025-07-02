<?php

namespace App\Form;

use App\Entity\Election;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminElectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jobTitle', null, [
                'attr' => [
                    'placeholder' => 'Ex : Secrétaire',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Poste',
            ])
            ->add('voteStartAt', DateTimeType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Date de début',
            ])
            ->add('voteEndAt', DateTimeType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Date de fin',
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
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Election::class,
        ]);
    }
}
