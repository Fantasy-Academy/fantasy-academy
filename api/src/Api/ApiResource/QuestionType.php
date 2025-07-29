<?php

declare(strict_types=1);

namespace FantasyAcademy\API\Api\ApiResource;

enum QuestionType: string
{
    case SingleSelect = 'single_select';
    case MultiSelect = 'multi_select';
    case Text = 'text';
    case Sort = 'sort';
    case Numeric = 'numeric';
}
