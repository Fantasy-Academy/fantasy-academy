<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Result;

/**
 * @phpstan-type UserSkillsPercentilesRowArray array{
 *     user_id: string,
 *     skill_analytical_percentile: int|float,
 *     skill_strategic_planning_percentile: int|float,
 *     skill_adaptability_percentile: int|float,
 *     skill_premier_league_knowledge_percentile: int|float,
 *     skill_risk_management_percentile: int|float,
 *     skill_decision_making_under_pressure_percentile: int|float,
 *     skill_financial_management_percentile: int|float,
 *     skill_long_term_vision_percentile: int|float,
 * }
 */
readonly final class UserSkillsPercentilesRow
{
    public function __construct(
        public string $userId,
        public float $skillAnalyticalPercentile,
        public float $skillStrategicPlanningPercentile,
        public float $skillAdaptabilityPercentile,
        public float $skillPremierLeagueKnowledgePercentile,
        public float $skillRiskManagementPercentile,
        public float $skillDecisionMakingUnderPressurePercentile,
        public float $skillFinancialManagementPercentile,
        public float $skillLongTermVisionPercentile,
    ) {
    }

    /**
     * @param UserSkillsPercentilesRowArray $data
     */
    public static function createFromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'],
            skillAnalyticalPercentile: (float) $data['skill_analytical_percentile'],
            skillStrategicPlanningPercentile: (float) $data['skill_strategic_planning_percentile'],
            skillAdaptabilityPercentile: (float) $data['skill_adaptability_percentile'],
            skillPremierLeagueKnowledgePercentile: (float) $data['skill_premier_league_knowledge_percentile'],
            skillRiskManagementPercentile: (float) $data['skill_risk_management_percentile'],
            skillDecisionMakingUnderPressurePercentile: (float) $data['skill_decision_making_under_pressure_percentile'],
            skillFinancialManagementPercentile: (float) $data['skill_financial_management_percentile'],
            skillLongTermVisionPercentile: (float) $data['skill_long_term_vision_percentile'],
        );
    }
}
