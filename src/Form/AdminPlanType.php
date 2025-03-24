<?php

namespace App\Form;

use App\Entity\Plan;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminPlanType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'attr' => [
                    'placeholder' => 'Ex : Étudiant',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Nom',
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'placeholder' => 'Ex : Plan spécial étudiants',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Description',
            ])
            ->add('price', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Ex : 5.00',
                    'step' => '0.01',
                    'min' => '0',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Annuel (€)',
            ])
            ->add('features', CollectionType::class, [
                'entry_type' => AdminFeatureType::class,
                'entry_options' => ['label' => false],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
            ])
            ->add('highlighted', CheckboxType::class, [
                'label' => 'Mis en avant',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'required' => false,
            ])
            ->add('priceVariable', CheckboxType::class, [
                'label' => 'Prix variable',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'mapped' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Plan::class,
        ]);
    }
}
