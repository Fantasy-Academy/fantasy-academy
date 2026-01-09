<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonbType;
use FantasyAcademy\API\Value\Answer;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type AnswerRow array{
 *     text_answer: null|string,
 *     numeric_answer: null|string,
 *     selected_choice_id: null|string,
 *     selected_choice_ids: null|array<string>,
 *     ordered_choice_ids: null|array<string>,
 * }
 */
final class AnswerDoctrineType extends JsonbType
{
    public const string NAME = 'answer';

    /**
     * @param AnswerRow $data
     */
    public static function createAnswerFromArray(array $data): Answer
    {
        $selectedChoiceIds = null;
        $orderedChoiceIds = null;

        if (is_array($data['selected_choice_ids'] ?? null)) {
            $selectedChoiceIds = array_map(
                callback: fn (string $uuid): Uuid => Uuid::fromString($uuid),
                array: $data['selected_choice_ids'],
            );
        }

        if (is_array($data['ordered_choice_ids'] ?? null)) {
            $orderedChoiceIds = array_map(
                callback: fn (string $uuid): Uuid => Uuid::fromString($uuid),
                array: $data['ordered_choice_ids'],
            );
        }

        return new Answer(
            textAnswer: $data["text_answer"],
            numericAnswer: $data["numeric_answer"] !== null ? (float) $data["numeric_answer"] : null,
            selectedChoiceId: $data["selected_choice_id"] !== null ? Uuid::fromString($data["selected_choice_id"]) : null,
            selectedChoiceIds: $selectedChoiceIds,
            orderedChoiceIds: $orderedChoiceIds,
        );
    }

    /**
     * @return AnswerRow
     */
    public static function transformAnswerToArray(Answer $value): array
    {
        $selectedChoiceIds = null;
        $orderedChoiceIds = null;

        if ($value->selectedChoiceIds !== null) {
            $selectedChoiceIds = array_map(
                callback: fn (Uuid $uuid): string => $uuid->toString(),
                array: $value->selectedChoiceIds,
            );
        }

        if ($value->orderedChoiceIds !== null) {
            $orderedChoiceIds = array_map(
                callback: fn (Uuid $uuid): string => $uuid->toString(),
                array: $value->orderedChoiceIds,
            );
        }

        return [
            'text_answer' => $value->textAnswer,
            'numeric_answer' => $value->numericAnswer !== null ? (string) $value->numericAnswer : null,
            'selected_choice_id' => $value->selectedChoiceId?->toString(),
            'selected_choice_ids' => $selectedChoiceIds,
            'ordered_choice_ids' => $orderedChoiceIds,
        ];
    }

    /**
     * @throws ConversionException
     */
    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): null|Answer
    {
        if ($value === null) {
            return null;
        }

        /** @var AnswerRow $jsonData */
        $jsonData = parent::convertToPHPValue($value, $platform);

        return self::createAnswerFromArray($jsonData);
    }

    /**
     * @param null|Answer $value
     * @throws ConversionException
     */
    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        $data = self::transformAnswerToArray($value);

        return parent::convertToDatabaseValue($data, $platform);
    }
}
