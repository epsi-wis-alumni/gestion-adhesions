<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanRenewalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        if ($options['renewal']) {
            $builder
                ->add('renewal', HiddenType::class, [
                    'attr' => [
                        'value' => 'false',
                    ],
                    'label' => null,
                ])
            ;
        } else {
            $builder
                ->add('renewal', HiddenType::class, [
                    'attr' => [
                        'value' => 'true',
                    ],
                    'label' => null,
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            "renewal" => true,
        ]);
    }
}
