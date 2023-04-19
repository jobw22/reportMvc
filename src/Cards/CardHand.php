<?php
namespace App\Cards;

class CardHand {
    public function __construct()
    {
        $this->hand = [];
    }

    public function addCard($card) {
        array_push($this->hand, $card);
    }

    public function getHand() {
        return $this->hand;
    }
}