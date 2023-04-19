<?php
namespace App\Cards;

use App\Cards\Card;

class DeckOfCards {
    private $suits = ["♠", "♥", "♣", "♦"];
    private $values = ["A", "2", "3", "4", "5", "6", "7", "8", "9", "10", "J", "D", "K"];

    private $deck = [];

    public function __construct() {
        foreach ($this->suits as $suit) {
            foreach ($this->values as $value) {
                $card = new CardGraphic($suit, $value);
                array_push($this->deck, $card);
            }
        }
    }

    public function getDeck() {
        return $this->deck;
    }

    public function shuffleDeck() {
        $shuffledDeck = $this->getDeck();
        shuffle($shuffledDeck);
        return $shuffledDeck;
    }

    public function removeCard($card) {
        unset($this->deck[$card]);
        //re-index
        $this->deck = array_values($this->deck);
    }
}