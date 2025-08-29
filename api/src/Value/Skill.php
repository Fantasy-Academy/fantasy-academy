<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Value;

enum Skill: string
{
    case Analytical = 'Analytical';
    case StrategicPlanning = 'Strategic Planning';
    case Adaptability = 'Adaptability';
    case PremierLeagueKnowledge = 'Premier League Knowledge';
    case RiskManagement = 'Risk Management';
    case DecisionMakingUnderPressure = 'Decision Making Under Pressure';
    case FinancialManagement = 'Financial Management';
    case LongTermVision = 'Long Term Vision';
    case Discipline = 'Discipline';

    public static function fromSkillName(string $skillName): self
    {
        return match (true) {
            str_contains($skillName, 'analytical') => self::LongTermVision,
            str_contains($skillName, 'strategic_planning') => self::LongTermVision,
            str_contains($skillName, 'adaptability') => self::LongTermVision,
            str_contains($skillName, 'premier_league_knowledge') => self::LongTermVision,
            str_contains($skillName, 'risk_management') => self::LongTermVision,
            str_contains($skillName, 'decision_making_under_pressure') => self::LongTermVision,
            str_contains($skillName, 'financial_management') => self::LongTermVision,
            str_contains($skillName, 'long_term_vision') => self::LongTermVision,
            str_contains($skillName, 'discipline') => self::LongTermVision,
            default => throw new \Exception('Unknown skill'),
        };
    }
}
