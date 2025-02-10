<?php

namespace App\Form;

use App\Entity\Settings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('newsletterAllowed', null, [
                'label' => 'Newsletter',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
            ])
            ->add('notificationsAllowed', null, [
                'label' => 'Notifications',
                'label_attr' => [
                    'class' => 'checkbox-switch',
                ],
                'attr' => [
                    'class' => 'js-notifications',
                    'data-notifications-target' => 'notifications',
                    'data-action' => 'change->notifications#toggle',
                ],
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event): void {
            $form = $event->getForm();
            $data = $event->getData();

            if (!$data) {
                return;
            }

            $value = $data->isNotificationsAllowed();

            $form->add('electionNotificationsAllowed', null, [
                'label' => 'Élections',
                'label_attr' => [
                    'class' => 'checkbox-switch'
                ],
                'data' => $value,
                'attr' => [
                    'class' => 'js-notification',
                    'data-notifications-target' => 'notification',
                ],
            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Settings::class,
        ]);
    }
}
