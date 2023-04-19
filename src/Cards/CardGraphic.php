<?php
namespace App\Cards;

class CardGraphic extends Card {
    public function __construct($suit, $value)
    {
        parent::__construct($suit, $value);
    }

    public function getAsString() {
        return "[{$this->suit}{$this->value}]";
    }
}