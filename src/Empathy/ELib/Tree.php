<?php

declare(strict_types=1);

namespace Empathy\ELib;

class Tree
{
    protected string $markup = '';

    protected string $url = '';

    public function getMarkup(): string
    {
        return $this->markup;
    }

    // taken from news controller
    protected function truncate(string $desc, int $max_length): string
    {
        if (strlen($desc) > $max_length) {
            $char = 'A';
            if (preg_match('/ /', substr($desc, 0, $max_length))) { // do trunc
                //while($max_length > 0 && $char != ' ')
                while (preg_match('/\w/', $char)) {
                    $char = substr($desc, $max_length, 1);
                    $max_length--;
                }
                //echo $max_length;
                $desc = substr($desc, 0, $max_length + 1);
                $desc = preg_replace('/\W$/', '', $desc).'...';
            }
        }
        return $desc;
    }

}
