<?php

declare(strict_types=1);

namespace FantasyAcademy\API\FormType;

use FantasyAcademy\API\FormData\GameweekFormData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<GameweekFormData>
 */
final class GameweekFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('season', IntegerType::class, [
                'label' => 'Season',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('number', IntegerType::class, [
                'label' => 'Gameweek Number',
                'required' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('title', TextType::class, [
                'label' => 'Title',
                'required' => false,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => ['class' => 'form-control', 'rows' => 4],
            ])
            ->add('startsAt', DateTimeType::class, [
                'label' => 'Starts At',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ])
            ->add('endsAt', DateTimeType::class, [
                'label' => 'Ends At',
                'required' => true,
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GameweekFormData::class,
        ]);
    }
}
