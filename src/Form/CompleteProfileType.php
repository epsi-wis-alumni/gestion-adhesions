<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\MemberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompleteProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', null, [
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Prénom',
            ])
            ->add('lastname', null, [
                'attr' => [
                    'placeholder' => 'Nom',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Nom',
            ])
            ->add('company', null, [
                'attr' => [
                    'placeholder' => 'Entreprise',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Entreprise',
            ])
            ->add('jobTitle', null, [
                'attr' => [
                    'placeholder' => 'Poste',
                ],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Poste',
            ])
            ->add('type', EnumType::class, [
                'class' => MemberType::class,
                'expanded' => true,
                'label' => 'Qui êtes-vous ?',
                'row_attr' => ['class' => 'mb-3'],
                'label_attr' => ['class' => 'radio-inline'],
                'choice_filter' => fn (MemberType $type) => MemberType::Undefined !== $type,
                'choice_label' => fn (MemberType $type) => match ($type) {
                    MemberType::Student => 'Étudiant',
                    MemberType::Alumni => 'Alumni',
                    MemberType::Partner => 'Partenaire',
                },
            ])
            ->add('studentCard', FileType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Carte étudiante',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.pdf, image/*, .doc, .docx, .odt',
                ],
            ])
            ->add('degree', FileType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Diplôme',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'accept' => '.pdf, image/*, .doc, .docx, .odt',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
