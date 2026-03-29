<?php

declare(strict_types=1);

namespace Empathy\ELib;

class YAML
{
    public static function save(mixed $data, string $file, bool $append = false): void
    {
        $s = new \Spyc();
        $yaml = self::dump($data);
        $mode = 'w';

        if ($append) {
            $mode = 'a';
        }

        $fh = fopen($file, $mode);
        if ($fh === false) {
            throw new \RuntimeException('Could not open file for writing: '.$file);
        }
        fwrite($fh, $yaml);
        fclose($fh);
    }

    public static function load(string $file): mixed
    {
        $s = new \Spyc();

        return $s->YAMLLoad($file);
    }

    public static function dump(mixed $data): string
    {
        $s = new \Spyc();

        return $s->YAMLDump($data, 4, 60);
    }

    public static function loadString(string $data): mixed
    {
        $s = new \Spyc();

        return $s->YAMLLoadString($data);
    }

    public static function objectToArray(mixed $object): mixed
    {
        if (!is_object($object) && !is_array($object)) {
            return $object;
        }
        if (is_object($object)) {
            $object = get_object_vars($object);
        }

        return array_map([self::class, 'objectToArray'], $object);
    }

}
