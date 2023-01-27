<?php

namespace Evotodi\PasswordMeterBundle\Services;

use Evotodi\PasswordMeterBundle\Interfaces\RequirementsInterface;
use Evotodi\PasswordMeterBundle\Models\Requirements;
use Evotodi\PasswordMeterBundle\Models\StringCollection;

final class RequirementsProvider implements RequirementsInterface
{
    public function getRequirements(): Requirements
    {
        $config = new Requirements();

        return $config;
    }
}