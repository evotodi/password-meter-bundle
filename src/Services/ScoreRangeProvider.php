<?php

namespace Evotodi\PasswordMeterBundle\Services;

use Evotodi\PasswordMeterBundle\Interfaces\ScoreRangeInterface;

class ScoreRangeProvider implements ScoreRangeInterface
{

    public function getScoreRange(): array
    {
        return [
            '40' => 'veryWeak', // 001 <= x <  040
            '80' => 'weak', // 040 <= x <  080
            '120' => 'medium', // 080 <= x <  120
            '180' => 'strong', // 120 <= x <  180
            '200' => 'veryStrong', // 180 <= x <  200
            '_' => 'perfect', //  >= 200
        ];
    }
}