<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, ['label' => 'Prénom',
                'attr' =>
                [
                    'class' => 'form-control'
                ]
            ])
            ->add('lastname', TextType::class, ['label' => 'Nom',
                'attr' =>
                [
                    'class' => 'form-control'
                ]
            ])
            ->add('email', EmailType::class, ['label' => 'Email',
                'attr' =>
                [
                    'id' => 'email',
                    'class' => 'form-control'
                ]
            ])
            ->add('objet', TextType::class, ['label' => 'Objet',
                    'attr' =>
                    [
                        'class' => 'form-control'
                    ]
                ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' =>
                    [
                        'class' => 'form-control'
                    ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}
