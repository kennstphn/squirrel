<?php

namespace App\Filesystem\Structures;

class Markdown
{
    public $meta, $markdown, $html;
    function __construct(string $markdown, string $html, array $meta = null){
        $this->meta = $meta ? (object)$meta : (object)[];
        $this->html = $html;
        $this->markdown = $markdown;
    }

    function __toString()
    {
        return $this->html;
    }

}