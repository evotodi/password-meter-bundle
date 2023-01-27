<?php

namespace Evotodi\PasswordMeterBundle\Interfaces;

interface ScoreRangeInterface
{
/**
     * Return the score range for password meter
     *
     * @return array
     */
    public function getScoreRange(): array;
}