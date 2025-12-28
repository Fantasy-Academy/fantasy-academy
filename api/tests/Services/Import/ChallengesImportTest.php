<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Tests\Services\Import;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use FantasyAcademy\API\Entity\Challenge;
use FantasyAcademy\API\Entity\Question;
use FantasyAcademy\API\Exceptions\ImportFailed;
use FantasyAcademy\API\Services\Import\ChallengesImport;
use FantasyAcademy\API\Value\QuestionType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

final class ChallengesImportTest extends ApiTestCase
{
    private EntityManagerInterface $entityManager;
    private ChallengesImport $importer;

    protected function setUp(): void
    {
        $client = self::createClient();
        $container = $client->getContainer();

        /** @var EntityManagerInterface $entityManager */
        $entityManager = $container->get(EntityManagerInterface::class);
        $this->entityManager = $entityManager;

        /** @var ChallengesImport $importer */
        $importer = $container->get(ChallengesImport::class);
        $this->importer = $importer;
    }

    public function testSuccessfulImportCreatesEntities(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);

        // Clear entity manager to ensure fresh queries from database
        $this->entityManager->clear();

        // Verify first challenge (ID from predictable provider)
        $challenge1 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000001'));
        $this->assertInstanceOf(Challenge::class, $challenge1);
        $this->assertSame('The Transfer Window Challenge', $challenge1->name);
        $this->assertSame('Make smart transfers', $challenge1->shortDescription);
        $this->assertSame('You have £100m to build your dream team. Choose wisely and maximize your points.', $challenge1->description);
        $this->assertSame('https://example.com/transfer.jpg', $challenge1->image);
        $this->assertSame(1000, $challenge1->maxPoints);
        $this->assertEquals(new DateTimeImmutable('2024-01-01 00:00:00'), $challenge1->startsAt);
        $this->assertEquals(new DateTimeImmutable('2024-12-31 23:59:59'), $challenge1->expiresAt);
        $this->assertSame('Consider player form and fixtures', $challenge1->hintText);
        $this->assertSame('https://example.com/hint.jpg', $challenge1->hintImage);

        // Verify second challenge
        $challenge2 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000002'));
        $this->assertInstanceOf(Challenge::class, $challenge2);
        $this->assertSame('Captain Choice Masterclass', $challenge2->name);
        $this->assertNull($challenge2->image);
        $this->assertNull($challenge2->hintText);

        // Verify questions were created
        // UUID sequence: 1=Challenge1, 2=Challenge2, 3-5=Q1 choices, 6=Q1, 7-10=Q2 choices, 11=Q2, 12=Q3, 13=Q4, 14-15=Q5 choices, 16=Q5

        // Debug: List all questions to see what UUIDs were actually created
        $allQuestions = $this->entityManager->getRepository(Question::class)->findAll();
        $questionCount = 0;
        foreach ($allQuestions as $q) {
            if (in_array($q->challenge->id->toString(), [
                '01933333-0000-7000-8000-000000000001',
                '01933333-0000-7000-8000-000000000002',
            ])) {
                $questionCount++;
            }
        }
        $this->assertSame(5, $questionCount, 'Should have created 5 questions');
    }

    public function testImportWithNumericQuestion(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        // Find the numeric question (UUID 12)
        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000012'));
        $this->assertInstanceOf(Question::class, $question);
        $this->assertSame('How many goals will your team score this gameweek?', $question->text);
        $this->assertSame(QuestionType::Numeric, $question->type);
        $this->assertNull($question->image);

        $this->assertNotNull($question->numericConstraint);
        $this->assertSame(0, $question->numericConstraint->min);
        $this->assertSame(50, $question->numericConstraint->max);
        $this->assertNull($question->choiceConstraint);
    }

    public function testImportWithSingleSelectQuestion(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        // Find the single select question (UUID 6, first question - choices are 3,4,5)
        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000006'));
        $this->assertInstanceOf(Question::class, $question);
        $this->assertSame('Who will you transfer in as your premium midfielder?', $question->text);
        $this->assertSame(QuestionType::SingleSelect, $question->type);
        $this->assertNull($question->image);
        $this->assertNull($question->numericConstraint);

        $this->assertNotNull($question->choiceConstraint);
        $this->assertCount(3, $question->choiceConstraint->choices);
        $this->assertSame(1, $question->choiceConstraint->minSelections);
        $this->assertSame(1, $question->choiceConstraint->maxSelections);

        // Verify choice details
        $choices = $question->choiceConstraint->choices;
        $this->assertSame('Mohamed Salah', $choices[0]->text);
        $this->assertSame('Liverpool star with great form', $choices[0]->description);
        $this->assertSame('https://example.com/salah.jpg', $choices[0]->image);

        $this->assertSame('Kevin De Bruyne', $choices[1]->text);
        $this->assertSame('Man City playmaker', $choices[1]->description);
        $this->assertNull($choices[1]->image);

        $this->assertSame('Bruno Fernandes', $choices[2]->text);
        $this->assertSame('Manchester United captain', $choices[2]->description);
        $this->assertNull($choices[2]->image);
    }

    public function testImportWithMultiSelectQuestion(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        // Find the multi select question (UUID 11, second question - choices are 7,8,9,10)
        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000011'));
        $this->assertInstanceOf(Question::class, $question);
        $this->assertSame('Select your defensive picks (choose 2)', $question->text);
        $this->assertSame(QuestionType::MultiSelect, $question->type);

        $this->assertNotNull($question->choiceConstraint);
        $this->assertCount(4, $question->choiceConstraint->choices);
        $this->assertSame(2, $question->choiceConstraint->minSelections);
        $this->assertSame(2, $question->choiceConstraint->maxSelections);
    }

    public function testImportWithTextQuestion(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        // Find the text question (UUID 13, 4th question)
        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000013'));
        $this->assertInstanceOf(Question::class, $question);
        $this->assertSame('Explain your transfer strategy in one sentence', $question->text);
        $this->assertSame(QuestionType::Text, $question->type);
        $this->assertSame('https://example.com/strategy.jpg', $question->image);
        $this->assertNull($question->numericConstraint);
        $this->assertNull($question->choiceConstraint);
    }

    public function testSkillsPercentageConversion(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);

        // First challenge uses integer percentages (25, 30, etc.) which should be converted to 0.25, 0.30, etc.
        $challenge1 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000001'));
        $this->assertInstanceOf(Challenge::class, $challenge1);
        $this->assertSame(0.25, $challenge1->skillAnalytical);
        $this->assertSame(0.30, $challenge1->skillStrategicPlanning);
        $this->assertSame(0.15, $challenge1->skillAdaptability);
        $this->assertSame(0.40, $challenge1->skillPremierLeagueKnowledge);
        $this->assertSame(0.20, $challenge1->skillRiskManagement);
        $this->assertSame(0.25, $challenge1->skillDecisionMakingUnderPressure);
        $this->assertSame(0.35, $challenge1->skillFinancialManagement);
        $this->assertSame(0.10, $challenge1->skillLongTermVision);
    }

    public function testSkillsDecimalPreserved(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);

        // Second challenge uses decimals (0.3, 0.2, etc.) which should be preserved
        $challenge2 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000002'));
        $this->assertInstanceOf(Challenge::class, $challenge2);
        $this->assertSame(0.3, $challenge2->skillAnalytical);
        $this->assertSame(0.2, $challenge2->skillStrategicPlanning);
        $this->assertSame(0.25, $challenge2->skillAdaptability);
        $this->assertSame(0.5, $challenge2->skillPremierLeagueKnowledge);
        $this->assertSame(0.4, $challenge2->skillRiskManagement);
        $this->assertSame(0.35, $challenge2->skillDecisionMakingUnderPressure);
        $this->assertSame(0.1, $challenge2->skillFinancialManagement);
        $this->assertSame(0.15, $challenge2->skillLongTermVision);
    }

    public function testQuestionBelongsToCorrectChallenge(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        $challenge1 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000001'));
        $challenge2 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000002'));

        // First 4 questions belong to challenge 1
        $question1 = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000006'));
        $this->assertInstanceOf(Question::class, $question1);
        $this->assertSame($challenge1, $question1->challenge);

        // Last question (UUID 16) belongs to challenge 2
        $question5 = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000016'));
        $this->assertInstanceOf(Question::class, $question5);
        $this->assertSame($challenge2, $question5->challenge);
    }

    public function testMissingChallengeColumnThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_missing_challenge_column.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage("column 'name': Missing required column");

        $this->importer->importFile($file);
    }

    public function testMissingQuestionColumnThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_missing_question_column.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage("column 'type': Missing required column");

        $this->importer->importFile($file);
    }

    public function testDuplicateChallengeIdThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_duplicate_challenge_id.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Duplicate challenge ID "C001"');

        $this->importer->importFile($file);
    }

    public function testInvalidChallengeReferenceThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_invalid_challenge_reference.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Non-existing challenge ID "C999"');

        $this->importer->importFile($file);
    }

    public function testEmptyChallengeIdThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_empty_challenge_id.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Missing challenge ID');

        $this->importer->importFile($file);
    }

    public function testEmptyQuestionChallengeIdThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_empty_question_challenge_id.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Empty challenge ID');

        $this->importer->importFile($file);
    }

    public function testSingleSheetThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_single_sheet.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('The workbook must contain at least two sheets');

        $this->importer->importFile($file);
    }

    public function testInvalidQuestionTypeThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_invalid_question_type.xlsx');

        $this->expectException(\ValueError::class);

        $this->importer->importFile($file);
    }

    public function testInvalidJsonChoicesThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_invalid_json_choices.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Invalid JSON format');

        $this->importer->importFile($file);
    }

    public function testEntitiesArePersistedInDatabase(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);

        // Clear entity manager to force fresh database queries
        $this->entityManager->clear();

        // Verify specific entities exist
        $challenge1 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000001'));
        $this->assertInstanceOf(Challenge::class, $challenge1);

        $challenge2 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000002'));
        $this->assertInstanceOf(Challenge::class, $challenge2);

        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000006'));
        $this->assertInstanceOf(Question::class, $question);
    }

    public function testChoiceQuestionsWithNullMinMaxSelections(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        // Last question (UUID 16) has choices but no min/max selections specified
        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000016'));
        $this->assertInstanceOf(Question::class, $question);
        $this->assertSame(QuestionType::SingleSelect, $question->type);

        $this->assertNotNull($question->choiceConstraint);
        $this->assertCount(2, $question->choiceConstraint->choices);
        $this->assertNull($question->choiceConstraint->minSelections);
        $this->assertNull($question->choiceConstraint->maxSelections);
    }

    public function testImportWithCorrectAnswers(): void
    {
        $file = $this->createUploadedFile('challenge_import_with_correct_answers.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        // Verify text question with correct answer
        $question1 = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000002'));
        $this->assertInstanceOf(Question::class, $question1);
        $this->assertNotNull($question1->correctAnswer);
        $this->assertEquals('Example text answer', $question1->correctAnswer->textAnswer);
        $this->assertNull($question1->correctAnswer->numericAnswer);

        // Verify numeric question with correct answer
        $question2 = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000003'));
        $this->assertInstanceOf(Question::class, $question2);
        $this->assertNotNull($question2->correctAnswer);
        $this->assertEquals(42.0, $question2->correctAnswer->numericAnswer);
        $this->assertNull($question2->correctAnswer->textAnswer);
    }

    public function testUpdateExistingChallengeWithCorrectAnswers(): void
    {
        // First import: create a challenge without correct answers
        $file1 = $this->createUploadedFile('challenge_import_valid.xlsx');
        $this->importer->importFile($file1);
        $this->entityManager->clear();

        // Verify question exists without correct answer
        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000006'));
        $this->assertInstanceOf(Question::class, $question);
        $this->assertNull($question->correctAnswer);

        // Second import: update the challenge with correct answers
        // The update modifies the existing challenge in place, preserving UUIDs
        $file2 = $this->createUploadedFile('challenge_import_update_with_correct_answers.xlsx');
        $this->importer->importFile($file2);
        $this->entityManager->clear();

        // Verify question was updated with correct answer
        // UUIDs are preserved during update: Challenge: UUID 1, Choices: 3-5, Question: 6
        $updatedQuestion = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000006'));
        $this->assertInstanceOf(Question::class, $updatedQuestion);
        $this->assertNotNull($updatedQuestion->correctAnswer);
        $this->assertNotNull($updatedQuestion->correctAnswer->selectedChoiceId);
        // Verify the choice text matches
        $this->assertNotNull($updatedQuestion->choiceConstraint);
        $salahChoice = null;
        foreach ($updatedQuestion->choiceConstraint->choices as $choice) {
            if ($choice->text === 'Mohamed Salah') {
                $salahChoice = $choice;
                break;
            }
        }
        $this->assertNotNull($salahChoice);
        $this->assertEquals($salahChoice->id, $updatedQuestion->correctAnswer->selectedChoiceId);
    }

    public function testChoicesMissingTextFieldThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_choices_missing_text.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Choice at index 0 is missing required field "text"');

        $this->importer->importFile($file);
    }

    public function testChoicesMissingDescriptionFieldThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_choices_missing_description.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Choice at index 1 is missing required field "description"');

        $this->importer->importFile($file);
    }

    public function testChoicesNotArrayThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_choices_not_array.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Must be a JSON array');

        $this->importer->importFile($file);
    }

    public function testChoiceElementNotObjectThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_choice_element_not_object.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Choice at index 0 must be an object with "text" and "description" fields');

        $this->importer->importFile($file);
    }

    public function testMissingGameweekColumnThrowsException(): void
    {
        $file = $this->createUploadedFile('challenge_import_missing_gameweek.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage("column 'gameweek': Missing required column");

        $this->importer->importFile($file);
    }

    public function testGameweekIsImportedCorrectly(): void
    {
        $file = $this->createUploadedFile('challenge_import_valid.xlsx');

        $this->importer->importFile($file);
        $this->entityManager->clear();

        $challenge1 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000001'));
        $this->assertInstanceOf(Challenge::class, $challenge1);
        $this->assertSame(1, $challenge1->gameweek);

        $challenge2 = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000002'));
        $this->assertInstanceOf(Challenge::class, $challenge2);
        $this->assertSame(2, $challenge2->gameweek);
    }

    public function testImportWithQuestionIdUpdatesExistingQuestion(): void
    {
        // First import creates the question
        $file1 = $this->createUploadedFile('challenge_import_valid.xlsx');
        $this->importer->importFile($file1);
        $this->entityManager->clear();

        // Get the created question UUID
        $question = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000006'));
        $this->assertInstanceOf(Question::class, $question);
        $this->assertSame('Who will you transfer in as your premium midfielder?', $question->text);

        // Second import updates the question by question_id with new text
        $file2 = $this->createUploadedFile('challenge_import_update_by_question_id.xlsx');
        $this->importer->importFile($file2);
        $this->entityManager->clear();

        // Verify the question text was updated
        $updatedQuestion = $this->entityManager->find(Question::class, Uuid::fromString('01933333-0000-7000-8000-000000000006'));
        $this->assertInstanceOf(Question::class, $updatedQuestion);
        $this->assertSame('Updated question text via question_id', $updatedQuestion->text);
    }

    public function testImportWithEmptyQuestionIdCreatesNewQuestion(): void
    {
        // Import file where question_id column exists but is empty
        $file = $this->createUploadedFile('challenge_import_empty_question_id.xlsx');
        $this->importer->importFile($file);
        $this->entityManager->clear();

        // Verify new questions were created with generated UUIDs
        $allQuestions = $this->entityManager->getRepository(Question::class)->findAll();
        $this->assertGreaterThan(0, count($allQuestions), 'Should have created questions');
    }

    public function testImportDeletesQuestionNotInImport(): void
    {
        // First import creates questions
        $file1 = $this->createUploadedFile('challenge_import_with_multiple_questions.xlsx');
        $this->importer->importFile($file1);
        $this->entityManager->clear();

        // Verify questions were created
        $challenge = $this->entityManager->find(Challenge::class, Uuid::fromString('01933333-0000-7000-8000-000000000001'));
        $this->assertInstanceOf(Challenge::class, $challenge);

        $questions = $this->entityManager->getRepository(Question::class)->findBy(['challenge' => $challenge]);
        $questionCountBefore = count($questions);
        $this->assertGreaterThan(1, $questionCountBefore, 'Should have created multiple questions');

        // Second import for same challenge has fewer questions - one is omitted
        $file2 = $this->createUploadedFile('challenge_import_with_fewer_questions.xlsx');
        $this->importer->importFile($file2);
        $this->entityManager->clear();

        // Verify question was deleted
        $questionsAfter = $this->entityManager->getRepository(Question::class)->findBy(['challenge' => $challenge]);
        $this->assertLessThan($questionCountBefore, count($questionsAfter), 'Question should have been deleted');
    }

    public function testImportFailsToDeleteQuestionWithAnswers(): void
    {
        // Use the ExpiredChallengeFixture which has a question with player answers
        // Import for that challenge without the question - should fail
        $file = $this->createUploadedFile('challenge_import_delete_answered_question.xlsx');

        $this->expectException(ImportFailed::class);
        $this->expectExceptionMessage('Cannot delete question');

        $this->importer->importFile($file);
    }

    private function createUploadedFile(string $filename): UploadedFile
    {
        $path = __DIR__ . '/../../imports/challenge/' . $filename;

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
