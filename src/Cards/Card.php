<?php

namespace App\Cards;

class Card
{
    protected $value;
    protected $suit;

    public function __construct($suit, $value)
    {
        $this->suit = $suit;
        $this->value = $value;
    }

    public function getAsWords()
    {
        switch ($this->suit) {
            case "♠":
                return "{$this->value} of spades";
            case "♥":
                return "{$this->value} of hearts";
            case "♣":
                return "{$this->value} of clubs";
            case "♦":
                return "{$this->value} of diamonds";
        }
    }
}
