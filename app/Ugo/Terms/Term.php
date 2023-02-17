<?php

namespace App\Ugo\Terms;

class Term
{
    public $term;
    // Construct a single term 
    function __construct(string $term)
    {
        $this->term = $term;
    }

    public function get(): array
    {
        return [
            "term" => $this->term,
        ];
    }
}
