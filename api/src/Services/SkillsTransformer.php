<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services;

use FantasyAcademy\API\Exceptions\UserNotFound;
use FantasyAcademy\API\Query\UserDisciplineQuery;
use FantasyAcademy\API\Result\UserSkillsPercentilesRow;
use FantasyAcademy\API\Result\UserSkillsPercentilesWithChangeRow;
use FantasyAcademy\API\Value\PlayerSkill;
use FantasyAcademy\API\Value\Skill;

readonly final class SkillsTransformer
{
    public function __construct(
        private UserDisciplineQuery $userDisciplineQuery,
    ) {
    }
    /**
     * @return array<PlayerSkill>
     */
    public function transformPercentilesRowToPlayerSkills(UserSkillsPercentilesRow $row): array
    {
        return [
            new PlayerSkill(
                name: Skill::Analytical->value,
                percentage: (int) round($row->skillAnalyticalPercentile),
                percentageChange: null,
            ),
            new PlayerSkill(
                name: Skill::StrategicPlanning->value,
                percentage: (int) round($row->skillStrategicPlanningPercentile),
                percentageChange: null,
            ),
            new PlayerSkill(
                name: Skill::Adaptability->value,
                percentage: (int) round($row->skillAdaptabilityPercentile),
                percentageChange: null,
            ),
            new PlayerSkill(
                name: Skill::PremierLeagueKnowledge->value,
                percentage: (int) round($row->skillPremierLeagueKnowledgePercentile),
                percentageChange: null,
            ),
            new PlayerSkill(
                name: Skill::RiskManagement->value,
                percentage: (int) round($row->skillRiskManagementPercentile),
                percentageChange: null,
            ),
            new PlayerSkill(
                name: Skill::DecisionMakingUnderPressure->value,
                percentage: (int) round($row->skillDecisionMakingUnderPressurePercentile),
                percentageChange: null,
            ),
            new PlayerSkill(
                name: Skill::FinancialManagement->value,
                percentage: (int) round($row->skillFinancialManagementPercentile),
                percentageChange: null,
            ),
            new PlayerSkill(
                name: Skill::LongTermVision->value,
                percentage: (int) round($row->skillLongTermVisionPercentile),
                percentageChange: null,
            ),
        ];
    }

    /**
     * @return array<PlayerSkill>
     */
    public function transformToPlayerSkills(UserSkillsPercentilesRow $row, string $userId): array
    {
        $skills = $this->transformPercentilesRowToPlayerSkills($row);

        try {
            $disciplinePercentage = $this->userDisciplineQuery->forPlayer($userId);

            $skills[] = new PlayerSkill(
                name: Skill::Discipline->value,
                percentage: (int) round($disciplinePercentage),
                percentageChange: null,
            );
        } catch (UserNotFound) {
            // If discipline data is not available, don't add the skill
        }

        return $skills;
    }

    /**
     * @return array<PlayerSkill>
     */
    public function transformPercentilesWithChangeRowToPlayerSkills(UserSkillsPercentilesWithChangeRow $row): array
    {
        return [
            new PlayerSkill(
                name: Skill::Analytical->value,
                percentage: (int) round($row->skillAnalyticalPercentile),
                percentageChange: $row->skillAnalyticalPercentilePrevious !== null
                    ? (int) round($row->skillAnalyticalPercentile - $row->skillAnalyticalPercentilePrevious)
                    : null,
            ),
            new PlayerSkill(
                name: Skill::StrategicPlanning->value,
                percentage: (int) round($row->skillStrategicPlanningPercentile),
                percentageChange: $row->skillStrategicPlanningPercentilePrevious !== null
                    ? (int) round($row->skillStrategicPlanningPercentile - $row->skillStrategicPlanningPercentilePrevious)
                    : null,
            ),
            new PlayerSkill(
                name: Skill::Adaptability->value,
                percentage: (int) round($row->skillAdaptabilityPercentile),
                percentageChange: $row->skillAdaptabilityPercentilePrevious !== null
                    ? (int) round($row->skillAdaptabilityPercentile - $row->skillAdaptabilityPercentilePrevious)
                    : null,
            ),
            new PlayerSkill(
                name: Skill::PremierLeagueKnowledge->value,
                percentage: (int) round($row->skillPremierLeagueKnowledgePercentile),
                percentageChange: $row->skillPremierLeagueKnowledgePercentilePrevious !== null
                    ? (int) round($row->skillPremierLeagueKnowledgePercentile - $row->skillPremierLeagueKnowledgePercentilePrevious)
                    : null,
            ),
            new PlayerSkill(
                name: Skill::RiskManagement->value,
                percentage: (int) round($row->skillRiskManagementPercentile),
                percentageChange: $row->skillRiskManagementPercentilePrevious !== null
                    ? (int) round($row->skillRiskManagementPercentile - $row->skillRiskManagementPercentilePrevious)
                    : null,
            ),
            new PlayerSkill(
                name: Skill::DecisionMakingUnderPressure->value,
                percentage: (int) round($row->skillDecisionMakingUnderPressurePercentile),
                percentageChange: $row->skillDecisionMakingUnderPressurePercentilePrevious !== null
                    ? (int) round($row->skillDecisionMakingUnderPressurePercentile - $row->skillDecisionMakingUnderPressurePercentilePrevious)
                    : null,
            ),
            new PlayerSkill(
                name: Skill::FinancialManagement->value,
                percentage: (int) round($row->skillFinancialManagementPercentile),
                percentageChange: $row->skillFinancialManagementPercentilePrevious !== null
                    ? (int) round($row->skillFinancialManagementPercentile - $row->skillFinancialManagementPercentilePrevious)
                    : null,
            ),
            new PlayerSkill(
                name: Skill::LongTermVision->value,
                percentage: (int) round($row->skillLongTermVisionPercentile),
                percentageChange: $row->skillLongTermVisionPercentilePrevious !== null
                    ? (int) round($row->skillLongTermVisionPercentile - $row->skillLongTermVisionPercentilePrevious)
                    : null,
            ),
        ];
    }

    /**
     * @param array{current: float, previous: null|float} $disciplineData
     * @return array<PlayerSkill>
     */
    public function transformToPlayerSkillsWithChange(
        UserSkillsPercentilesWithChangeRow $row,
        string $userId,
        array $disciplineData
    ): array {
        $skills = $this->transformPercentilesWithChangeRowToPlayerSkills($row);

        $skills[] = new PlayerSkill(
            name: Skill::Discipline->value,
            percentage: (int) round($disciplineData['current']),
            percentageChange: $disciplineData['previous'] !== null
                ? (int) round($disciplineData['current'] - $disciplineData['previous'])
                : null,
        );

        return $skills;
    }
}
