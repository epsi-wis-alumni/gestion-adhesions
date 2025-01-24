<?php

namespace App\Form;

use App\Entity\Newsletter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminNewsletterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('objet', TextType::class, [
                'attr' => ['class' => ''],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Objet',
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'class' => 'form-widget p-3',
                    'data-markdown-target' => 'input',
                    'data-action' => 'input->markdown#render',
                    'aria-label' => 'Type markdown into this box',
                    'autocomplete' => 'off',
                    'placeholder' => '# Corps de la Newsletter',
                    'rows' => '8',
                ],
                'row_attr' => ['class' => 'mb-0'],
                'label' => False,
            ])
            ->add('cta', TextType::class, [
                'attr' => ['class' => ''],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Call To Action',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Newsletter::class,
        ]);
    }
}
