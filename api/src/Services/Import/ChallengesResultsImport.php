<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services\Import;

use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Repository\PlayerChallengeAnswerRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly final class ChallengesResultsImport
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private PlayerChallengeAnswerRepository $playerChallengeAnswerRepository,
    ) {
    }

    public function importFile(UploadedFile $file): void
    {
        $path = $file->getPathname();

        // Instruction: parse the excel
        // Instruction: Get playerChallengeAnswer from repository for each row and call evaluate() with parsed points

        $this->entityManager->flush();
    }
}
