<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Repository;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Exceptions\QuestionNotFound;
use Symfony\Component\Uid\Uuid;

readonly final class QuestionRepository
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function add(Question $question): void
    {
        $this->entityManager->persist($question);
    }

    /**
     * @throws QuestionNotFound
     */
    public function get(Uuid $id): Question
    {
        $row = $this->entityManager->find(Question::class, $id);

        if ($row instanceof Question) {
            return $row;
        }

        throw new QuestionNotFound();
    }
}
