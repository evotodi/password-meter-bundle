<?php

namespace Evotodi\PasswordMeterBundle\Interfaces;

use Evotodi\PasswordMeterBundle\Models\Requirements;

interface RequirementsInterface
{
    /**
     * Return the requirements for password meter
     *
     * @return Requirements
     */
    public function getRequirements(): Requirements;
}