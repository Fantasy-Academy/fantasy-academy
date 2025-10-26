<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Result;

/**
 * @phpstan-type UserSkillsPercentilesWithChangeRowArray array{
 *     user_id: string,
 *     skill_analytical_percentile: int|float,
 *     skill_strategic_planning_percentile: int|float,
 *     skill_adaptability_percentile: int|float,
 *     skill_premier_league_knowledge_percentile: int|float,
 *     skill_risk_management_percentile: int|float,
 *     skill_decision_making_under_pressure_percentile: int|float,
 *     skill_financial_management_percentile: int|float,
 *     skill_long_term_vision_percentile: int|float,
 *     skill_analytical_percentile_previous: int|float|null,
 *     skill_strategic_planning_percentile_previous: int|float|null,
 *     skill_adaptability_percentile_previous: int|float|null,
 *     skill_premier_league_knowledge_percentile_previous: int|float|null,
 *     skill_risk_management_percentile_previous: int|float|null,
 *     skill_decision_making_under_pressure_percentile_previous: int|float|null,
 *     skill_financial_management_percentile_previous: int|float|null,
 *     skill_long_term_vision_percentile_previous: int|float|null,
 * }
 */
readonly final class UserSkillsPercentilesWithChangeRow
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
        public null|float $skillAnalyticalPercentilePrevious,
        public null|float $skillStrategicPlanningPercentilePrevious,
        public null|float $skillAdaptabilityPercentilePrevious,
        public null|float $skillPremierLeagueKnowledgePercentilePrevious,
        public null|float $skillRiskManagementPercentilePrevious,
        public null|float $skillDecisionMakingUnderPressurePercentilePrevious,
        public null|float $skillFinancialManagementPercentilePrevious,
        public null|float $skillLongTermVisionPercentilePrevious,
    ) {
    }

    /**
     * @param UserSkillsPercentilesWithChangeRowArray $data
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
            skillAnalyticalPercentilePrevious: isset($data['skill_analytical_percentile_previous']) ? (float) $data['skill_analytical_percentile_previous'] : null,
            skillStrategicPlanningPercentilePrevious: isset($data['skill_strategic_planning_percentile_previous']) ? (float) $data['skill_strategic_planning_percentile_previous'] : null,
            skillAdaptabilityPercentilePrevious: isset($data['skill_adaptability_percentile_previous']) ? (float) $data['skill_adaptability_percentile_previous'] : null,
            skillPremierLeagueKnowledgePercentilePrevious: isset($data['skill_premier_league_knowledge_percentile_previous']) ? (float) $data['skill_premier_league_knowledge_percentile_previous'] : null,
            skillRiskManagementPercentilePrevious: isset($data['skill_risk_management_percentile_previous']) ? (float) $data['skill_risk_management_percentile_previous'] : null,
            skillDecisionMakingUnderPressurePercentilePrevious: isset($data['skill_decision_making_under_pressure_percentile_previous']) ? (float) $data['skill_decision_making_under_pressure_percentile_previous'] : null,
            skillFinancialManagementPercentilePrevious: isset($data['skill_financial_management_percentile_previous']) ? (float) $data['skill_financial_management_percentile_previous'] : null,
            skillLongTermVisionPercentilePrevious: isset($data['skill_long_term_vision_percentile_previous']) ? (float) $data['skill_long_term_vision_percentile_previous'] : null,
        );
    }
}
