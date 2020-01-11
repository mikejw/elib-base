<?php

namespace Empathy\ELib;

class Tree
{
    protected $markup;
    protected $url;

    public function getMarkup()
    {
        return $this->markup;
    }

    // taken from news controller
    protected function truncate($desc, $max_length)
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
                $desc = substr($desc, 0, $max_length+1);
                $desc = preg_replace('/\W$/', '', $desc).'...';
            }
        }
        return $desc;
    }

}
