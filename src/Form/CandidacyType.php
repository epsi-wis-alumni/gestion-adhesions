<?php

namespace App\Form;

use App\Entity\Candidacy;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CandidacyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('presentation', TextType::class, [
                'row_attr' => ['class' => 'mb-3'], 
            ])
            ->add('save', SubmitType::class, [
                'attr' => ['class' => 'btn btn-primary'], 
                'row_attr' => ['class' => 'mb-3'], 
                'label' => 'Candidacyr',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidacy::class,
        ]);
    }
}
