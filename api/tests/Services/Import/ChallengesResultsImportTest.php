<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Services\Import;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\PlayerChallengeAnswer;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Repository\PlayerChallengeAnswerRepository;
use FantasyAcademy\API\Exceptions\ImportResultsWarning;
use FantasyAcademy\API\Services\Import\ChallengesResultsImport;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallenge2Fixture;
use FantasyAcademy\API\Tests\DataFixtures\ExpiredChallengeFixture;
use FantasyAcademy\API\Tests\DataFixtures\PlayerChallengeAnswerFixture;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class ChallengesResultsImportTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;
    private ChallengesResultsImport $importer;

    protected function setUp(): void
    {
        $container = static::getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;

        /** @var ChallengesResultsImport $importer */
        $importer = $container->get(ChallengesResultsImport::class);
        $this->importer = $importer;
    }

    public function testSuccessfulImportUpdatesPointsInDatabase(): void
    {
        $file = $this->createUploadedFile('results_import_valid.xlsx');

        $this->importer->importFile($file);

        $this->entityManager->clear();

        // Verify USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID points were updated
        $answer1 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer1);
        $this->assertSame(850, $answer1->points, 'Points should be updated from 800 to 850');

        // Verify USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID points were updated
        $answer2 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer2);
        $this->assertSame(950, $answer2->points, 'Points should be updated from 900 to 950');

        // Verify USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID points remain same
        $answer3 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer3);
        $this->assertSame(900, $answer3->points, 'Points should remain 900');
    }

    public function testImportEvaluatesChallenges(): void
    {
        $file = $this->createUploadedFile('results_import_valid.xlsx');

        // Verify challenge is not evaluated before import
        $challenge = $this->entityManager->find(
            Challenge::class,
            Uuid::fromString(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID)
        );
        $this->assertInstanceOf(Challenge::class, $challenge);
        $evaluatedAtBefore = $challenge->evaluatedAt;

        $this->importer->importFile($file);

        // Clear and re-fetch
        $this->entityManager->clear();
        $challenge = $this->entityManager->find(
            Challenge::class,
            Uuid::fromString(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID)
        );
        $this->assertInstanceOf(Challenge::class, $challenge);

        // Verify challenge is now evaluated
        $this->assertNotNull($challenge->evaluatedAt, 'Challenge should be evaluated');
        if ($evaluatedAtBefore !== null) {
            $this->assertGreaterThanOrEqual(
                $evaluatedAtBefore->getTimestamp(),
                $challenge->evaluatedAt->getTimestamp(),
                'evaluatedAt should be updated or remain the same'
            );
        }
    }

    public function testImportEvaluatesMultipleChallengesInOneFile(): void
    {
        $file = $this->createUploadedFile('results_import_multiple_challenges.xlsx');

        $this->importer->importFile($file);

        // Clear entity manager
        $this->entityManager->clear();

        // Verify both challenges were evaluated
        $challenge1 = $this->entityManager->find(
            Challenge::class,
            Uuid::fromString(ExpiredChallengeFixture::EXPIRED_CHALLENGE_ID)
        );
        $this->assertInstanceOf(Challenge::class, $challenge1);
        $this->assertNotNull($challenge1->evaluatedAt, 'Challenge 1 should be evaluated');

        $challenge2 = $this->entityManager->find(
            Challenge::class,
            Uuid::fromString(ExpiredChallenge2Fixture::EXPIRED_CHALLENGE_2_ID)
        );
        $this->assertInstanceOf(Challenge::class, $challenge2);
        $this->assertNotNull($challenge2->evaluatedAt, 'Challenge 2 should be evaluated');
    }

    public function testFormulaCalculationInPoints(): void
    {
        $file = $this->createUploadedFile('results_import_valid.xlsx');

        $this->importer->importFile($file);

        // Clear entity manager
        $this->entityManager->clear();

        // Verify USER_2_EXPIRED_CHALLENGE_2_ANSWER_ID has calculated formula points (500+250=750)
        $answer = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_2_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer);
        $this->assertSame(750, $answer->points, 'Formula =500+250 should calculate to 750');
    }

    public function testMissingPointsSheetThrowsException(): void
    {
        $file = $this->createUploadedFile('results_import_missing_sheet.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Sheet "Points" not found in the uploaded file.');

        $this->importer->importFile($file);
    }

    public function testMissingIdColumnThrowsException(): void
    {
        $file = $this->createUploadedFile('results_import_missing_columns.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Column "points" not found in the Points sheet.');

        $this->importer->importFile($file);
    }

    public function testInvalidUuidsAreSkipped(): void
    {
        $file = $this->createUploadedFile('results_import_invalid_uuids.xlsx');

        $this->expectException(ImportResultsWarning::class);
        $this->expectExceptionMessageMatches('/1 results imported successfully/');
        $this->expectExceptionMessageMatches('/1 IDs not found/');
        $this->expectExceptionMessageMatches('/not-a-valid-uuid/');

        $this->importer->importFile($file);
    }

    public function testNonExistentAnswerIdsThrowWarning(): void
    {
        $file = $this->createUploadedFile('results_import_with_warnings.xlsx');

        try {
            $this->importer->importFile($file);
            $this->fail('Expected ImportResultsWarning to be thrown');
        } catch (ImportResultsWarning $e) {
            // Verify warning details
            $this->assertSame(2, $e->importedCount, 'Should import 2 valid answers');
            $this->assertCount(2, $e->missingIds, 'Should have 2 missing IDs');
            $this->assertContains('99999999-9999-9999-9999-999999999999', $e->missingIds);
            $this->assertContains('88888888-8888-8888-8888-888888888888', $e->missingIds);

            // Verify message format
            $this->assertStringContainsString('2 results imported successfully', $e->getMessage());
            $this->assertStringContainsString('2 IDs not found', $e->getMessage());
        }

        // Verify the valid answers were still imported
        $this->entityManager->clear();
        $answer1 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer1);
        $this->assertSame(100, $answer1->points, 'Valid answer should have been imported');

        $answer2 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer2);
        $this->assertSame(300, $answer2->points, 'Valid answer should have been imported');
    }

    public function testEmptyRowsAreSkipped(): void
    {
        $file = $this->createUploadedFile('results_import_empty_rows.xlsx');

        // Should not throw exception, just skip empty rows
        $this->importer->importFile($file);

        // Verify only the valid row was imported
        $this->entityManager->clear();
        $answer = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer);
        $this->assertSame(400, $answer->points, 'Only valid row should be imported');
    }

    public function testDataIsPersisted(): void
    {
        $file = $this->createUploadedFile('results_import_valid.xlsx');

        // Get original points
        $answer1 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer1);
        $originalPoints = $answer1->points;

        $this->importer->importFile($file);

        // Clear entity manager to force fresh database queries
        $this->entityManager->clear();

        // Verify data persisted in database
        $answer1Reloaded = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer1Reloaded);
        $this->assertNotSame($originalPoints, $answer1Reloaded->points, 'Points should be updated in database');
        $this->assertSame(850, $answer1Reloaded->points);
    }

    public function testImportWithValidDataDoesNotThrowException(): void
    {
        $file = $this->createUploadedFile('results_import_valid.xlsx');

        // Should complete without throwing exception
        $this->importer->importFile($file);

        // Verify import completed by checking data
        $this->entityManager->clear();
        $answer = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer);
        $this->assertNotNull($answer->points);
    }

    public function testColumnIndexConversion(): void
    {
        // Test that the convertColumnIndexToLetter method works correctly
        // by importing a file where columns might not be in standard A, B, C order
        $file = $this->createUploadedFile('results_import_valid.xlsx');

        $this->importer->importFile($file);

        // If column conversion failed, the import would fail or give wrong results
        $this->entityManager->clear();
        $answer = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer);
        $this->assertSame(850, $answer->points);
    }

    public function testMultipleAnswersForSameChallengeAreImported(): void
    {
        $file = $this->createUploadedFile('results_import_valid.xlsx');

        $this->importer->importFile($file);

        // Verify multiple answers for the same challenge were all imported
        $this->entityManager->clear();

        $answer1 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $answer2 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_2_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $answer3 = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_3_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );

        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer1);
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer2);
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer3);

        $this->assertSame(850, $answer1->points);
        $this->assertSame(950, $answer2->points);
        $this->assertSame(900, $answer3->points);
    }

    public function testZeroPointsAreImported(): void
    {
        $file = $this->createUploadedFile('results_import_zero_points.xlsx');

        $this->importer->importFile($file);

        $this->entityManager->clear();
        $answer = $this->entityManager->find(
            PlayerChallengeAnswer::class,
            Uuid::fromString(PlayerChallengeAnswerFixture::USER_1_EXPIRED_CHALLENGE_1_ANSWER_ID)
        );
        $this->assertInstanceOf(PlayerChallengeAnswer::class, $answer);
        $this->assertSame(0, $answer->points, 'Zero points should be imported correctly');
    }

    private function createUploadedFile(string $filename): UploadedFile
    {
        $path = __DIR__ . '/../../imports/results/' . $filename;

        if (!file_exists($path)) {
            throw new \RuntimeException(sprintf('Test file not found: %s', $path));
        }

        // Create a temporary copy of the file
        $tempPath = tempnam(sys_get_temp_dir(), 'test_import_');
        assert($tempPath !== false);
        copy($path, $tempPath);

        return new UploadedFile(
            path: $tempPath,
            originalName: $filename,
            mimeType: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            test: true,
        );
    }
}
