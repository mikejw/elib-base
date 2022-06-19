<?php

namespace Empathy\ELib\Queue;

class Stats
{
    const SEMAPHORE_ID = 100;
    const SEGMENT_ID = 200;

    private static $handle;
    private static $semaphore;
    private static $shared_vars = array('stats' => 1);

    public static function getVarKey($var_name)
    {
        //echo $var_name;
        //print_r(self::$shared_vars);
        //echo "\n";
        return self::$shared_vars[$var_name];
    }

    public static function getHandle()
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

    public static function release()
    {
        \shm_detach(self::$handle);
        \sem_release(self::$semaphore);
    }

    public static function store($key, $value)
    {
        self::getHandle();
        if (!\shm_put_var(self::$handle, self::getVarKey($key), $value)) {
            \sem_remove(self::$semaphore);
            \shm_remove(self::$handle);
            die('couldn\'t write to shared memory.');
        }
        self::release();
    }

    public static function retrieve($key)
    {
        self::getHandle();
        $data = \shm_get_var(self::$handle, self::getVarKey($key));
        self::release();

        return $data;
    }

}
