<?php

namespace App\Tests\Behat;

class SharedExceptionStorage
{
    private ?\Exception $lastException = null;

    public function setException(?\Exception $exception): void
    {
        $this->lastException = $exception;
    }

    public function getException(): ?\Exception
    {
        return $this->lastException;
    }

    public function clearException(): void
    {
        $this->lastException = null;
    }
}
