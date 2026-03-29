<?php

declare(strict_types=1);

/**
 * Optional queue dependency; referenced only when DriverPheanstalk is used.
 */

namespace Pheanstalk;

class ReservedJob
{
    public function getData(): string
    {
        return '';
    }
}

class Pheanstalk
{
    public function __construct(mixed $host)
    {
    }

    public function useTube(string $tube): self
    {
        return $this;
    }

    public function put(string $data): void
    {
    }

    public function watch(string $tube): self
    {
        return $this;
    }

    public function ignore(string $tube): self
    {
        return $this;
    }

    public function reserve(): ReservedJob
    {
        return new ReservedJob();
    }

    public function delete(object $job): void
    {
    }

    /** @return array<string, mixed> */
    public function stats(): array
    {
        return [];
    }
}
