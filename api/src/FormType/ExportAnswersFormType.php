<?php

declare(strict_types=1);

namespace FantasyAcademy\API\FormType;

use FantasyAcademy\API\FormData\ExportAnswersFormData;
use FantasyAcademy\API\Query\ChallengeQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<ExportAnswersFormData>
 */
final class ExportAnswersFormType extends AbstractType
{
    public function __construct(
        readonly private ChallengeQuery $challengeQuery,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $challenges = $this->challengeQuery->getAll();
        
        $choices = [];
        foreach ($challenges as $challenge) {
            $evaluatedText = $challenge->evaluatedAt !== null 
                ? $challenge->evaluatedAt->format('d.m.Y')
                : 'not evaluated';

            $label = sprintf(
                '%s (%s - %s, evaluated: %s)',
                $challenge->name,
                $challenge->startsAt->format('d.m.Y'),
                $challenge->expiresAt->format('d.m.Y'),
                $evaluatedText
            );
            
            $choices[$label] = $challenge->id;
        }

        $builder->add('challengeIds', ChoiceType::class, [
            'label' => 'Select challenges to export:',
            'choices' => $choices,
            'multiple' => true,
            'expanded' => true,
            'required' => true,
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ExportAnswersFormData::class,
        ]);
    }
}
