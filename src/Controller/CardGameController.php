<?php

namespace App\Controller;

// Cards
use App\Cards\Card;
use App\Cards\CardGraphic;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;
// Symfony
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CardGameController extends AbstractController
{
    #[Route("/card", name: "card")]
    public function cards(): Response
    {
        return $this->render('cards/card.html.twig');
    }

    #[Route("/card/deck", name: "card/deck")]
    public function deck(): Response
    {
        $deck = new DeckOfCards();

        $stringDeck = [];
        foreach ($deck->getDeck() as $card) {
            array_push($stringDeck, $card->getAsString());
        }

        $data = [
            'DeckOfCards' => $stringDeck
        ];
        return $this->render('cards/deck.html.twig', $data);
    }

    #[Route("/card/deck/shuffle", name: "card/deck/shuffle")]
    public function shuffled(
        SessionInterface $session
    ): Response {
        $deck = new DeckOfCards();
        $session->set("deck", $deck);

        $stringDeck = [];
        foreach ($deck->shuffleDeck() as $card) {
            array_push($stringDeck, $card->getAsString());
        }

        $data = [
            'DeckOfCards' => $stringDeck
        ];
        return $this->render('cards/shuffle.html.twig', $data);
    }

    #[Route("/card/deck/draw", name: "card/deck/draw")]
    public function draw(
        SessionInterface $session
    ): Response {
        // retrive the count of cards left from the session
        if ($session->get("deck")) {
            $deck = $session->get("deck");
            $hand = $session->get("hand");
            $session->set("cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("cards_left");
        } else {
            // make a new deck
            $deck = new DeckOfCards();
            // make a new hand
            $hand = new CardHand();
            $session->set("deck", $deck);
            $session->set("hand", $hand);
            $session->set("cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("cards_left");
        }

        $cardNum = random_int(0, $cardsLeft - 1);
        $stringDeck = [];
        foreach ($deck->getDeck() as $card) {
            array_push($stringDeck, $card->getAsString());
        }
        $oneCard = $stringDeck[$cardNum];

        // add the card to the hand
        $hand->addCard($oneCard);

        //remove the card just picked from the deck
        $deck->removeCard($cardNum);
        $amountCards = count($deck->getDeck());

        //Set the new count of the cards in the session
        $session->set("cards_left", $amountCards);
        $data = [
            'aCard' => $oneCard,
            'cardCount' => $session->get("cards_left")
        ];
        return $this->render('cards/draw.html.twig', $data);
    }

    #[Route("/card/deck/draw/{num<\d+>}", name: "draw_cards")]
    public function drawSeveral(
        int $num,
        SessionInterface $session
    ): Response {
        //retrive the count of cards left from the session
        if ($session->get("deck")) {
            $deck = $session->get("deck");
            $cardsLeft = $session->get("cards_left");
            $hand = $session->get("hand");
        } else {
            $deck = new DeckOfCards();
            $session->set("deck", $deck);
            $hand = new CardHand();
            $session->set("cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("cards_left");
        }
        // Throw exception if amount of cards to draw exceeds the deck
        if ($num > $cardsLeft) {
            throw new \Exception("Can not draw more cards than there are in the deck!");
        }

        $theCards = [];
        for ($i = 0; $i < $num; $i++) {
            $cardsLeft = $session->get("cards_left");
            $cardNum = random_int(0, $cardsLeft - 1);
            $stringDeck = [];

            foreach ($deck->getDeck() as $card) {
                array_push($stringDeck, $card->getAsString());
            }
            $oneCard = $stringDeck[$cardNum];
            array_push($theCards, $oneCard);
            //remove the card just picked from the deck
            $deck->removeCard($cardNum);
            $amountCards = count($deck->getDeck());
            $session->set("cards_left", $amountCards);
        }
        // var_dump($deck);
        $data = [
            'theCards' => $theCards,
            'cardCount' => $session->get("cards_left")
        ];
        return $this->render('cards/drawSeveral.html.twig', $data);
    }
}
