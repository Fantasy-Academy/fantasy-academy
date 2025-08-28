<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Query;

use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Result\PlayerQuestionAnswerRow;
use Symfony\Component\Uid\Uuid;

/**
 * @phpstan-import-type PlayerQuestionAnswerRowArray from PlayerQuestionAnswerRow
 */
readonly final class PlayerQuestionAnswerQuery
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return array<PlayerQuestionAnswerRow>
     */
    public function getForChallenge(Uuid $challengeId): array
    {
        $sql = <<<SQL
WITH choice_map AS (
  SELECT
    q.id AS question_id,
    (choice->>'id')::uuid AS choice_id,
    choice->>'text' AS choice_text
  FROM question q
  LEFT JOIN LATERAL jsonb_array_elements(q.choice_constraint->'choices') AS choice ON TRUE
)
SELECT
  pca.user_id AS player_id,
  c.id AS challenge_id,
  q.id AS question_id,
  q.text AS question_name,
  paq.text_answer,
  paq.numeric_answer,
  (
    SELECT cm.choice_text
    FROM choice_map cm
    WHERE cm.question_id = q.id
      AND cm.choice_id = paq.selected_choice_id
  ) AS selected_choice_text,
  CASE
    WHEN paq.selected_choice_ids IS NULL THEN NULL
    ELSE (
      SELECT jsonb_agg(cm.choice_text ORDER BY e.ord)
      FROM jsonb_array_elements_text(paq.selected_choice_ids) WITH ORDINALITY AS e(id_txt, ord)
      LEFT JOIN choice_map cm
        ON cm.question_id = q.id
       AND cm.choice_id = e.id_txt::uuid
    )
  END AS selected_choice_texts,
  CASE
    WHEN paq.ordered_choice_ids IS NULL THEN NULL
    ELSE (
      SELECT jsonb_agg(cm.choice_text ORDER BY e.ord)
      FROM jsonb_array_elements_text(paq.ordered_choice_ids) WITH ORDINALITY AS e(id_txt, ord)
      LEFT JOIN choice_map cm
        ON cm.question_id = q.id
       AND cm.choice_id = e.id_txt::uuid
    )
  END AS ordered_choice_texts,
  paq.challenge_answer_id
FROM challenge AS c
JOIN question AS q
  ON q.challenge_id = c.id
JOIN player_answered_question AS paq
  ON paq.question_id = q.id
JOIN player_challenge_answer AS pca
  ON pca.id = paq.challenge_answer_id
 AND pca.challenge_id = c.id
WHERE c.id = :challengeId;
SQL;

        /** @var array<PlayerQuestionAnswerRowArray> $rows */
        $rows = $this->connection->executeQuery($sql, [
            'challengeId' => $challengeId->toString(),
        ])->fetchAllAssociative();

        return array_map(
            callback: fn (array $row): PlayerQuestionAnswerRow => PlayerQuestionAnswerRow::createFromArray($row),
            array: $rows,
        );
    }
}
