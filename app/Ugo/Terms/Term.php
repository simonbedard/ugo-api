<?php
namespace App\Ugo\Terms;

class Term
{
    // Construct a single term 
    function __construct(string $term) {
        $this->term = $term;
    }

    public function get(){
        return [
            "term" => $this->term,
        ];
    }
}