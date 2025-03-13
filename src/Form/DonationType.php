<?php

namespace App\Form;

use App\Entity\Donation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('amount', NumberType::class, [
            'attr' => [
                "type" => "number",
                'step' => '0.01',
                "min" => '0.01',
                "max" => '999999.99',
            ],
            'row_attr' => ['class' => 'mb-3'],
            'label' => 'Prix',
            'required' => true,
            'html5' => true,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Donation::class,
        ]);
    }
}
