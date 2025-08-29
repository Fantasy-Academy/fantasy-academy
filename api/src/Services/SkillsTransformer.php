<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Services;

use FantasyAcademy\API\Result\UserSkillsPercentilesRow;
use FantasyAcademy\API\Value\PlayerSkill;
use FantasyAcademy\API\Value\Skill;

readonly final class SkillsTransformer
{
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
}