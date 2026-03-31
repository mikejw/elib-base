<?php

declare(strict_types=1);

namespace Empathy\ELib\Queue;

class Stats
{
    public const SEMAPHORE_ID = 100;
    public const SEGMENT_ID = 200;

    private static mixed $handle = null;

    private static mixed $semaphore = null;

    /** @var array<string, int> */
    private static array $shared_vars = ['stats' => 1];

    public static function getVarKey(string $var_name): int
    {
        //echo $var_name;
        //print_r(self::$shared_vars);
        //echo "\n";
        return self::$shared_vars[$var_name];
    }

    public static function getHandle(): void
    {
        self::$semaphore = \sem_get(self::SEMAPHORE_ID, 1, 0644);

        if (self::$semaphore === false) {
            die('Failed to create semaphore.');
        }

        if (!\sem_acquire(self::$semaphore)) {
            die("Can't acquire semaphore");
        }
        self::$handle = \shm_attach(self::SEGMENT_ID, 16384, 0600);

        if (self::$handle === false) {
            die('Failed to attach shared memory');
        }
    }

    public static function release(): void
    {
        \shm_detach(self::$handle);
        \sem_release(self::$semaphore);
    }

    public static function store(string $key, mixed $value): void
    {
        self::getHandle();
        if (!\shm_put_var(self::$handle, self::getVarKey($key), $value)) {
            \sem_remove(self::$semaphore);
            \shm_remove(self::$handle);
            die('couldn\'t write to shared memory.');
        }
        self::release();
    }

    public static function retrieve(string $key): mixed
    {
        self::getHandle();
        $data = \shm_get_var(self::$handle, self::getVarKey($key));
        self::release();

        return $data;
    }

}
