<?php

declare(strict_types=1);

namespace Empathy\ELib\Queue;

use Empathy\ELib\YAML;

class Job
{
    private mixed $id = null;

    private mixed $queued_at = null;

    private mixed $body = null;

    /*
      private $time_to_run;
      private $priority;
      private $delay;
    */

    private mixed $data = null;

    private mixed $data_s = null;

    private mixed $tube = null;

    /** @var list<string> */
    private array $serialized_vars = [];

    /**
     * @param list<mixed> $args
     */
    public function __construct(array $args)
    {
        $this->serialized_vars = [
            'id', 'queued_at', 'body', 'tube'];
        switch (sizeof($args)) {
            case 2:
                $this->init($args[0], $args[1]);
                break;
            case 1:
                $this->initEmpty($args[0]);
                break;
        }
    }

    public function init(mixed $body, ?string $tube): void
    {
        $this->tube = $tube;
        $this->id = uniqid();
        $this->queued_at = time();
        $this->body = $body;
        $this->serialize();
    }

    public function initEmpty(mixed $data): void
    {
        $this->setData($data);
        $this->deserialize();
    }

    public function setData(mixed $data): void
    {
        $this->data_s = $data;
    }

    public function getTube(): mixed
    {
        return $this->tube;
    }

    public function getBody(): mixed
    {
        return $this->body;
    }

    public function getID(): mixed
    {
        return $this->id;
    }

    public function serialize(): void
    {
        $data = [];
        foreach ($this->serialized_vars as $v) {
            $data[$v] = $this->$v;
        }
        $this->data_s = YAML::dump($data);
    }

    public function getSerialized(): mixed
    {
        return $this->data_s;
    }

    public function deserialize(): void
    {
        $data = YAML::loadString($this->data_s);
        foreach ($this->serialized_vars as $v) {
            $this->$v = $data[$v];
        }
    }

}
