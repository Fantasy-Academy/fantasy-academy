<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\UniqueConstraint;
use FantasyAcademy\API\Exceptions\ChallengeExpired;
use FantasyAcademy\API\Exceptions\NotEnoughChoices;
use FantasyAcademy\API\Exceptions\TooManyChoices;
use JetBrains\PhpStorm\Immutable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;

#[Entity]
#[UniqueConstraint(fields: ['user', 'challenge'])]
class PlayerChallengeAnswer
{
    #[Column(nullable: true)]
    public int|null $points = null;

    #[Immutable(Immutable::PRIVATE_WRITE_SCOPE)]
    #[Column(nullable: true)]
    public null|DateTimeImmutable $answeredAt = null;

    /** @var Collection<int, PlayerAnsweredQuestion> */
    #[OneToMany(targetEntity: PlayerAnsweredQuestion::class, mappedBy: 'challengeAnswer', cascade: ['persist'], orphanRemoval: true)]
    private Collection $answeredQuestions;

    public function __construct(
        #[Id]
        #[Immutable]
        #[Column(type: UuidType::NAME, unique: true)]
        public Uuid $id,

        #[ManyToOne]
        #[JoinColumn(nullable: false)]
        readonly public Challenge $challenge,

        #[ManyToOne]
        #[JoinColumn(nullable: false)]
        readonly public User $user,
    ) {
        $this->answeredQuestions = new ArrayCollection();
    }

    /**
     * @param null|array<Uuid> $orderedChoiceIds
     * @param null|array<Uuid> $selectedChoiceIds
     *
     * @throws ChallengeExpired
     * @throws NotEnoughChoices
     * @throws TooManyChoices
     */
    public function answerQuestion(
        DateTimeImmutable $answeredAt,
        Question $question,
        null|string $textAnswer,
        null|float $numericAnswer,
        null|Uuid $selectedChoiceId,
        null|array $selectedChoiceIds,
        null|array $orderedChoiceIds,
    ): void
    {
        if ($answeredAt->getTimestamp() > $this->challenge->expiresAt->getTimestamp()) {
            throw new ChallengeExpired();
        }

        $minSelections = $question->choiceConstraint?->minSelections;
        $maxSelections = $question->choiceConstraint?->maxSelections;
        $selectedChoicesCount = count($selectedChoiceIds ?? []);

        if ($minSelections !== null && $selectedChoicesCount < $minSelections) {
            throw new NotEnoughChoices($selectedChoicesCount);
        }

        if ($maxSelections !== null && $selectedChoicesCount > $maxSelections) {
            throw new TooManyChoices($selectedChoicesCount);
        }

        $existingAnswer = $this->answerForQuestion($question);
        $this->answeredAt = $answeredAt;

        if ($existingAnswer !== null) {
            $existingAnswer->changeAnswer(
                $answeredAt,
                textAnswer: $textAnswer,
                numericAnswer: $numericAnswer,
                selectedChoiceId: $selectedChoiceId,
                selectedChoiceIds: $selectedChoiceIds,
                orderedChoiceIds: $orderedChoiceIds,
            );

            return;
        }

        $answeredQuestion = new PlayerAnsweredQuestion(
            $question,
            $this,
            $answeredAt,
            textAnswer: $textAnswer,
            numericAnswer: $numericAnswer,
            selectedChoiceId: $selectedChoiceId,
            selectedChoiceIds: $selectedChoiceIds,
            orderedChoiceIds: $orderedChoiceIds,
        );

        $this->answeredQuestions->add($answeredQuestion);
    }

    private function answerForQuestion(Question $question): null|PlayerAnsweredQuestion
    {
        return array_find(
            array: $this->answeredQuestions->toArray(),
            callback: fn ($answeredQuestion): bool => $answeredQuestion->question->id->equals($question->id)
        );
    }
}
