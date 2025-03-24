<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanPriceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price', NumberType::class, [
                'attr' => [
                    "type" => "number",
                    'step' => '0.01',
                    "value" => $options["price"],
                    "min" => $options["price"],
                    "max" => 10000,
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Prix',
                'required' => true,
                'mapped' => false,
                'html5' => true,
            ])
            ->add('plan', HiddenType::class, [
                'attr' => [
                    "value" => $options["planId"],
                ],
                'label' => null,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'price' => null,
            'planId' => null,
        ]);
    }
}
