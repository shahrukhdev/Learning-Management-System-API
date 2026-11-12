<?php

namespace App\Enums;

enum ProgressGrade: string
{
    case A_PLUS = 'A+';
    case A = 'A';
    case B_PLUS = 'B+';
    case B = 'B';
    case C = 'C';
    case NEEDS_IMPROVEMENT = 'Needs Improvement';

    /**
     * Grades counted as "Strong Results".
     *
     * @return array<int, string>
     */
    public static function strongValues(): array
    {
        return [self::A_PLUS->value, self::A->value];
    }

    /**
     * Grades counted as "Needs Attention".
     *
     * @return array<int, string>
     */
    public static function attentionValues(): array
    {
        return [self::C->value, self::NEEDS_IMPROVEMENT->value];
    }
}
