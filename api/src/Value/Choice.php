<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-type ChoiceArray array{
 *     id: string,
 *     text: string,
 *     description: null|string,
 *     image: null|string,
 * }
 */
readonly final class Choice
{
    public function __construct(
        public Uuid $id,
        public string $text,
        public null|string $description,
        public null|string $image,
    ) {}

    /**
     * @param ChoiceArray $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: Uuid::fromString($data['id']),
            text: $data['text'],
            description: $data['description'],
            image: $data['image'],
        );
    }

    /**
     * @return ChoiceArray
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'text' => $this->text,
            'description' => $this->description,
            'image' => $this->image,
        ];
    }
}
