<?php

namespace Evotodi\PasswordMeterBundle\Models;

use JetBrains\PhpStorm\ArrayShape;

class Result
{
    private int $score = 0;
    private string $status = '';
    private float $percent = 0;
    /**
     * @var array{error: string}
     */
    private array $errors = [];

    public function getScore(): int
    {
        return $this->score;
    }

    public function setScore(int $score): void
    {
        $this->score = $score;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }

    public function setPercent(float $percent): void
    {
        if ($percent >= 100.0) {
            $percent = 100;
        }
        $this->percent = $percent;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    public function setErrors(array $errors): void
    {
        $this->errors = $errors;
    }
}