<?php

namespace App\Form;

use App\Entity\Election;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminElectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('jobTitle', null, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex : Secrétaire',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Poste',
            ])
            ->add('voteStartAt', null, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Date de début',
            ])
            ->add('voteEndAt', null, [
                'attr' => [
                    'class' => 'form-control',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Date de fin',
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Enregistrer',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Election::class,
        ]);
    }
}
