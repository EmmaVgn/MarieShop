<?php

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('rating', IntegerType::class, [
            //     'label' => false,
            //     'attr' => [
            //         'placeholder' => 'Merci de nous laisser votre note (entre 0 et 5)',
            //         'min' => 0,
            //         'max' => 5,
            //         'step' => 1,
            //     ]
            // ])
            ->add('rating', HiddenType::class, [
                'label' => false,
            ])
            ->add('fullname', TextType::class, [
                'label' => 'Nom complet :',
                'attr' => [
                    'placeholder' => 'Votre nom complet (ex: prénom et nom)'
                ]
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Votre commentaire :',
                'attr' => [
                    'placeholder' => 'N\'hésitez pas à être très précis, cela aidera les autres utilisateurs !'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
