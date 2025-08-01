<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\StateProvider;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use FantasyAcademy\API\Api\ApiResource\ChallengeDetailResponse;
use FantasyAcademy\API\Api\ApiResource\Choice;
use FantasyAcademy\API\Api\ApiResource\ChoiceQuestionConstraint;
use FantasyAcademy\API\Api\ApiResource\NumericQuestionConstraint;
use FantasyAcademy\API\Api\ApiResource\Question;
use FantasyAcademy\API\Api\ApiResource\QuestionType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Uid\Uuid;

/**
 * @implements ProviderInterface<ChallengeDetailResponse>
 */
readonly final class ChallengeDetailProvider implements ProviderInterface
{
    public function __construct(
        private Security $security,
    ) {}

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): ChallengeDetailResponse
    {
        $user = $this->security->getUser();
        $answeredAt = null;

        if ($user !== null) {
            $answeredAt = new \DateTimeImmutable();
        }

        $questions = [
            new Question(
                id: Uuid::fromString('0c9c7063-49bf-41b6-85ab-543f7b36412f'),
                text: 'Some random question?',
                type: QuestionType::MultiSelect,
                image: 'https://placecats.com/800/600',
                numericConstraint: null,
                choiceConstraint: new ChoiceQuestionConstraint(
                    choices: [
                        new Choice(
                            id: Uuid::fromString('0a2c6f08-3851-49db-8af7-01ea54f07a80'),
                            text: 'Some choice',
                            description: 'I dont know some description',
                            image: 'https://placecats.com/400/300',
                        ),
                        new Choice(
                            id: Uuid::fromString('0a2c6f08-3851-49db-8af7-01ea54f07a82'),
                            text: 'Other choice',
                            description: 'I dont know some description',
                            image: 'https://placecats.com/400/300',
                        ),
                    ],
                    minSelections: 1,
                    maxSelections: 2,
                ),
            ),
            new Question(
                id: Uuid::fromString('0c9c7063-49bf-41b6-85ab-543f7b3641af'),
                text: 'Some other question?',
                type: QuestionType::Numeric,
                image: 'https://placecats.com/800/600',
                numericConstraint: new NumericQuestionConstraint(
                    min: 1,
                    max: 20,
                ),
                choiceConstraint: null,
            ),
        ];

        return new ChallengeDetailResponse(
            id: Uuid::fromString('52a9de01-5f68-4c65-8443-ff04e1fe2642'),
            name: 'Name of the challenge',
            shortDescription: 'Short description',
            description: 'Description of the challenge',
            image: 'https://placecats.com/800/600',
            addedAt: new \DateTimeImmutable('2025-06-06 12:00:00'),
            startsAt: new \DateTimeImmutable('2025-07-29 12:00:00'),
            expiresAt: new \DateTimeImmutable('2025-09-06 12:00:00'),
            answeredAt: $answeredAt,
            isStarted: true,
            isExpired: false,
            isAnswered: $user !== null,
            isEvaluated: false,
            questions: $questions,
            hintText: 'Some kind of hint',
            hintImage: 'https://placecats.com/800/600'
        );
    }
}
