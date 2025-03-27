<?php

namespace App\Form;

use App\Entity\Language;
use App\Entity\Skill;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Enum\JobOfferType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class JobOfferSortingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('types', ChoiceType::class, [
                'choices' => $options['job_offer_choices'],
                'expanded' => true,
                'multiple' => true,
                'label' => 'Type d\'offre',
                'required' => false,
            ])
            ->add('categories', ChoiceType::class, [
                'choices' => $options['job_offer_categories'],
                'choice_label' => function ($category) {
                    return $category->getName();
                },
                'choice_value' => 'id',
                'expanded' => true,
                'multiple' => true,
                'label' => 'Catégories',
                'required' => false,
            ])
            ->add('experience', ChoiceType::class, [
                'choices' => $options['job_offer_experience'],
                'choice_label' => function ($value, $key) {
                    return $key;
                },
                'choice_value' => function ($value) {
                    return $value;
                },
                'expanded' => true,
                'multiple' => true,
                'label' => 'Niveau d\'expérience',
                'required' => false,
            ])
            ->add('languages', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'name',
                'multiple' => true,
                'autocomplete' => true,
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Langues',
                'required' => false,
            ])
            ->add('skills', EntityType::class, [
                'class' => Skill::class,
                'choice_label' => 'name',
                'multiple' => true,
                'autocomplete' => true,
                'attr' => [
                    'class' => 'form-select',
                ],
                'label' => 'Spécialité',
                'required' => false,
            ])
            ->add('location', TextType::class, [
                'label' => 'Localisation',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'job_offer_choices' => JobOfferType::getChoices(),
            'job_offer_categories' => [],
            'job_offer_experience' => [
                '0-2 ans' => '[0,2]',
                '3-7 ans' => '[3,7]',
                '8-15 ans' => '[8,15]',
                '16 ans et +' => '[16,null]',
            ],
        ]);
    }
}
