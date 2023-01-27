<?php

namespace Evotodi\PasswordMeterBundle\Tests;

use Evotodi\PasswordMeterBundle\Models\Requirements;

class YieldReq
{
    public function __construct(
        public string $password,
        public Requirements $requirements,
        public int $count,
        public array $expect
    )
    {
    }
}