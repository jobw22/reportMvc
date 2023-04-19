<?php
namespace App\Cards;

class Card
{
    protected $value;

    public function __construct($suit, $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }
}
