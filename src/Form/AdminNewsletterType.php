<?php

namespace App\Form;

use App\Entity\MailTemplate;
use App\Entity\Newsletter;
use App\Repository\MailTemplateRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\ChoiceList\Loader\CallbackChoiceLoader;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdminNewsletterType extends AbstractType
{
    public function __construct(
        protected MailTemplateRepository $mailTemplateRepository,
    )
    {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $mailTemplateRepository = $this->mailTemplateRepository;

        $builder
            ->add('object', TextType::class, [
                'attr' => ['class' => ''],
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Object',
            ])
            ->add('body', TextareaType::class, [
                'attr' => [
                    'class' => 'p-3',
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
            ->add('template', ChoiceType::class, [
                'row_attr' => ['class' => 'mb-3'],
                'label' => 'Choisissez un template',
                'placeholder' => 'Choisissez un template...',
                'choice_label' => 'label',
                'choice_loader' => new CallbackChoiceLoader(static function() use ($mailTemplateRepository): array {
                    $templates = $mailTemplateRepository->findAll();
                    usort($templates, fn (MailTemplate $a, MailTemplate $b) => $a->getLabel() <=> $b->getLabel());
                    return $templates;
                })
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
