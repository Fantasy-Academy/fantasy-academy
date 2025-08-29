<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Query;

use Doctrine\DBAL\Connection;
use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Result\UserSkillsPercentilesRow;

/**
 * @phpstan-import-type UserSkillsPercentilesRowArray from UserSkillsPercentilesRow
 */
readonly final class UserSkillsPercentilesQuery
{
    public function __construct(
        private Connection $connection,
    ) {
    }

    /**
     * @return array<UserSkillsPercentilesRow>
     */
    public function all(): array
    {
        $sql = <<<SQL
WITH skill_sums AS (
  SELECT
    pca.user_id,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_analytical, 0))::double precision AS skill_analytical_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_strategic_planning, 0))::double precision AS skill_strategic_planning_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_adaptability, 0))::double precision AS skill_adaptability_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_premier_league_knowledge, 0))::double precision AS skill_premier_league_knowledge_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_risk_management, 0))::double precision AS skill_risk_management_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_decision_making_under_pressure, 0))::double precision AS skill_decision_making_under_pressure_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_financial_management, 0))::double precision AS skill_financial_management_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_long_term_vision, 0))::double precision AS skill_long_term_vision_pts
  FROM player_challenge_answer pca
  JOIN challenge c ON c.id = pca.challenge_id
  GROUP BY pca.user_id
),
ranks AS (
  SELECT
    s.*,
    DENSE_RANK() OVER (ORDER BY s.skill_analytical_pts)                     AS dr_analytical,
    DENSE_RANK() OVER (ORDER BY s.skill_strategic_planning_pts)             AS dr_strategic_planning,
    DENSE_RANK() OVER (ORDER BY s.skill_adaptability_pts)                   AS dr_adaptability,
    DENSE_RANK() OVER (ORDER BY s.skill_premier_league_knowledge_pts)       AS dr_premier_league_knowledge,
    DENSE_RANK() OVER (ORDER BY s.skill_risk_management_pts)                AS dr_risk_management,
    DENSE_RANK() OVER (ORDER BY s.skill_decision_making_under_pressure_pts) AS dr_decision_making_under_pressure,
    DENSE_RANK() OVER (ORDER BY s.skill_financial_management_pts)           AS dr_financial_management,
    DENSE_RANK() OVER (ORDER BY s.skill_long_term_vision_pts)               AS dr_long_term_vision
  FROM skill_sums s
),
bounds AS (
  SELECT
    r.*,
    MAX(dr_analytical)                     OVER () AS max_dr_analytical,
    MAX(dr_strategic_planning)             OVER () AS max_dr_strategic_planning,
    MAX(dr_adaptability)                   OVER () AS max_dr_adaptability,
    MAX(dr_premier_league_knowledge)       OVER () AS max_dr_premier_league_knowledge,
    MAX(dr_risk_management)                OVER () AS max_dr_risk_management,
    MAX(dr_decision_making_under_pressure) OVER () AS max_dr_decision_making_under_pressure,
    MAX(dr_financial_management)           OVER () AS max_dr_financial_management,
    MAX(dr_long_term_vision)               OVER () AS max_dr_long_term_vision
  FROM ranks r
)
SELECT
  user_id,
  CASE WHEN max_dr_analytical = 1
       THEN 100.0
       ELSE ((dr_analytical - 1)::double precision / (max_dr_analytical - 1)) * 100.0
  END AS skill_analytical_percentile,

  CASE WHEN max_dr_strategic_planning = 1
       THEN 100.0
       ELSE ((dr_strategic_planning - 1)::double precision / (max_dr_strategic_planning - 1)) * 100.0
  END AS skill_strategic_planning_percentile,

  CASE WHEN max_dr_adaptability = 1
       THEN 100.0
       ELSE ((dr_adaptability - 1)::double precision / (max_dr_adaptability - 1)) * 100.0
  END AS skill_adaptability_percentile,

  CASE WHEN max_dr_premier_league_knowledge = 1
       THEN 100.0
       ELSE ((dr_premier_league_knowledge - 1)::double precision / (max_dr_premier_league_knowledge - 1)) * 100.0
  END AS skill_premier_league_knowledge_percentile,

  CASE WHEN max_dr_risk_management = 1
       THEN 100.0
       ELSE ((dr_risk_management - 1)::double precision / (max_dr_risk_management - 1)) * 100.0
  END AS skill_risk_management_percentile,

  CASE WHEN max_dr_decision_making_under_pressure = 1
       THEN 100.0
       ELSE ((dr_decision_making_under_pressure - 1)::double precision / (max_dr_decision_making_under_pressure - 1)) * 100.0
  END AS skill_decision_making_under_pressure_percentile,

  CASE WHEN max_dr_financial_management = 1
       THEN 100.0
       ELSE ((dr_financial_management - 1)::double precision / (max_dr_financial_management - 1)) * 100.0
  END AS skill_financial_management_percentile,

  CASE WHEN max_dr_long_term_vision = 1
       THEN 100.0
       ELSE ((dr_long_term_vision - 1)::double precision / (max_dr_long_term_vision - 1)) * 100.0
  END AS skill_long_term_vision_percentile
FROM bounds
ORDER BY user_id;
SQL;

        /** @var array<UserSkillsPercentilesRowArray> $rows */
        $rows = $this->connection->executeQuery($sql)->fetchAllAssociative();

        return array_map(
            callback: fn (array $row): UserSkillsPercentilesRow => UserSkillsPercentilesRow::createFromArray($row),
            array: $rows,
        );
    }

    public function forPlayer(string $playerId): UserSkillsPercentilesRow
    {
        $sql = <<<SQL
WITH skill_sums AS (
  SELECT
    pca.user_id,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_analytical, 0))::double precision AS skill_analytical_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_strategic_planning, 0))::double precision AS skill_strategic_planning_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_adaptability, 0))::double precision AS skill_adaptability_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_premier_league_knowledge, 0))::double precision AS skill_premier_league_knowledge_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_risk_management, 0))::double precision AS skill_risk_management_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_decision_making_under_pressure, 0))::double precision AS skill_decision_making_under_pressure_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_financial_management, 0))::double precision AS skill_financial_management_pts,
    SUM(COALESCE(pca.points, 0) * COALESCE(c.skill_long_term_vision, 0))::double precision AS skill_long_term_vision_pts
  FROM player_challenge_answer pca
  JOIN challenge c ON c.id = pca.challenge_id
  GROUP BY pca.user_id
),
ranks AS (
  SELECT
    s.*,
    DENSE_RANK() OVER (ORDER BY s.skill_analytical_pts)                     AS dr_analytical,
    DENSE_RANK() OVER (ORDER BY s.skill_strategic_planning_pts)             AS dr_strategic_planning,
    DENSE_RANK() OVER (ORDER BY s.skill_adaptability_pts)                   AS dr_adaptability,
    DENSE_RANK() OVER (ORDER BY s.skill_premier_league_knowledge_pts)       AS dr_premier_league_knowledge,
    DENSE_RANK() OVER (ORDER BY s.skill_risk_management_pts)                AS dr_risk_management,
    DENSE_RANK() OVER (ORDER BY s.skill_decision_making_under_pressure_pts) AS dr_decision_making_under_pressure,
    DENSE_RANK() OVER (ORDER BY s.skill_financial_management_pts)           AS dr_financial_management,
    DENSE_RANK() OVER (ORDER BY s.skill_long_term_vision_pts)               AS dr_long_term_vision
  FROM skill_sums s
),
bounds AS (
  SELECT
    r.*,
    MAX(dr_analytical)                     OVER () AS max_dr_analytical,
    MAX(dr_strategic_planning)             OVER () AS max_dr_strategic_planning,
    MAX(dr_adaptability)                   OVER () AS max_dr_adaptability,
    MAX(dr_premier_league_knowledge)       OVER () AS max_dr_premier_league_knowledge,
    MAX(dr_risk_management)                OVER () AS max_dr_risk_management,
    MAX(dr_decision_making_under_pressure) OVER () AS max_dr_decision_making_under_pressure,
    MAX(dr_financial_management)           OVER () AS max_dr_financial_management,
    MAX(dr_long_term_vision)               OVER () AS max_dr_long_term_vision
  FROM ranks r
)
SELECT
  user_id,
  CASE WHEN max_dr_analytical = 1
       THEN 100.0
       ELSE ((dr_analytical - 1)::double precision / (max_dr_analytical - 1)) * 100.0
  END AS skill_analytical_percentile,
  CASE WHEN max_dr_strategic_planning = 1
       THEN 100.0
       ELSE ((dr_strategic_planning - 1)::double precision / (max_dr_strategic_planning - 1)) * 100.0
  END AS skill_strategic_planning_percentile,
  CASE WHEN max_dr_adaptability = 1
       THEN 100.0
       ELSE ((dr_adaptability - 1)::double precision / (max_dr_adaptability - 1)) * 100.0
  END AS skill_adaptability_percentile,
  CASE WHEN max_dr_premier_league_knowledge = 1
       THEN 100.0
       ELSE ((dr_premier_league_knowledge - 1)::double precision / (max_dr_premier_league_knowledge - 1)) * 100.0
  END AS skill_premier_league_knowledge_percentile,
  CASE WHEN max_dr_risk_management = 1
       THEN 100.0
       ELSE ((dr_risk_management - 1)::double precision / (max_dr_risk_management - 1)) * 100.0
  END AS skill_risk_management_percentile,
  CASE WHEN max_dr_decision_making_under_pressure = 1
       THEN 100.0
       ELSE ((dr_decision_making_under_pressure - 1)::double precision / (max_dr_decision_making_under_pressure - 1)) * 100.0
  END AS skill_decision_making_under_pressure_percentile,
  CASE WHEN max_dr_financial_management = 1
       THEN 100.0
       ELSE ((dr_financial_management - 1)::double precision / (max_dr_financial_management - 1)) * 100.0
  END AS skill_financial_management_percentile,
  CASE WHEN max_dr_long_term_vision = 1
       THEN 100.0
       ELSE ((dr_long_term_vision - 1)::double precision / (max_dr_long_term_vision - 1)) * 100.0
  END AS skill_long_term_vision_percentile
FROM bounds
WHERE user_id = :user_id
SQL;

        /** @var UserSkillsPercentilesRowArray|false $row */
        $row = $this->connection->executeQuery($sql, ['user_id' => $playerId])->fetchAssociative();

        if ($row === false) {
            throw new UserNotFound();
        }

        return UserSkillsPercentilesRow::createFromArray($row);
    }
}
