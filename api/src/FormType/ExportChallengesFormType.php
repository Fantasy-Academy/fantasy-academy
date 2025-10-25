<?php

declare(strict_types=1);

namespace FantasyAcademy\API\FormType;

use FantasyAcademy\API\FormData\ExportChallengesFormData;
use FantasyAcademy\API\Result\ChallengeRow;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ExportChallengesFormData>
 */
final class ExportChallengesFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var array<ChallengeRow> $challenges */
        $challenges = $options['challenges'];

        $choices = [];
        foreach ($challenges as $challenge) {
            // Format: 'value' => 'value' (label doesn't matter, we render manually)
            $choices[$challenge->id] = $challenge->id;
        }

        $builder->add('challengeIds', ChoiceType::class, [
            'label' => false,
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true,
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExportChallengesFormData::class,
            'challenges' => [],
        ]);

        $resolver->setRequired('challenges');
        $resolver->setAllowedTypes('challenges', 'array');
    }
}
