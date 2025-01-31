<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('allowNewsletters', null, [
                // 'row_attr' => [
                //     'class' => 'mb-3',
                // ],
                'label' => 'Newsletter',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
            ])
            ->add('allowNotifications', null, [
                'label' => 'Notifications',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
