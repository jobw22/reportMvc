<?php

namespace App\Controller;

// Cards
use App\Cards\Card;
use App\Cards\CardGraphic;
use App\Cards\CardHand;
use App\Cards\DeckOfCards;
// Symfony
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class JSONApiController extends AbstractController
{
    #[Route("api", name: "api")]
    public function cards(
        SessionInterface $session
    ): Response {
        if ($session->get("api_deck")) {
            $deck = $session->get("api_deck");
            $session->set("api_cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("api_cards_left");
        } else {
            // make a new deck
            $deck = new DeckOfCards();
            // make a new hand
            $hand = new CardHand();
            $session->set("api_deck", $deck);
            $session->set("api_cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("api_cards_left");
        }
        $data = [
            'cardCount' => $session->get("api_cards_left")
        ];
        return $this->render('api.html.twig', $data);
    }

    #[Route("/api/quote", name:"api_quote")]
    public function quote(): Response
    {
        date_default_timezone_set("Europe/Stockholm");
        $number = random_int(0, 4);

        $date = date("Y/m/d");
        $time = date("H:i:sa");

        $quotes = [
            'Gentlemen, you cant fight in here. This is the war room. (President Merkin Muffley)',
            'Clothes make the man. Naked people have little or no influence in society. (Mark Twain)',
            'I, for one, welcome our new insect overlords. (Kent Brockman)',
            'It takes two to lie: one to lie and one to listen. (Homer Simpson)',
            'Loneliness and cheeseburgers are a dangerous mix.'
        ];

        $data = [$quotes[$number], $date, $time];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck", name:"api_deck", methods:['GET'])]
    public function deck(): Response
    {
        $deck = new DeckOfCards();
        $stringDeck = [];
        foreach ($deck->getDeck() as $card) {
            array_push($stringDeck, $card->getAsWords());
        }

        $response = new JsonResponse($stringDeck);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/shuffle", name:"api_shuffle", methods:['POST'])]
    public function shuffle(
        SessionInterface $session
    ): Response {
        // set a new deck in the session
        $deck = new DeckOfCards();
        $session->set("api_deck", $deck);

        $stringDeck = [];
        foreach ($deck->shuffleDeck() as $card) {
            array_push($stringDeck, $card->getAsWords());
        }

        $response = new JsonResponse($stringDeck);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw", name: "api_draw", methods:['POST'])]
    public function draw(
        SessionInterface $session
    ): Response {
        //retrive the count of cards left from the session
        if ($session->get("api_deck")) {
            $deck = $session->get("api_deck");
            $session->set("api_cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("api_cards_left");
        } else {
            // make a new deck
            $deck = new DeckOfCards();
            // make a new hand
            $hand = new CardHand();
            $session->set("api_deck", $deck);
            $session->set("api_cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("api_cards_left");
        }

        $cardNum = random_int(0, $cardsLeft - 1);
        $stringDeck = [];
        foreach ($deck->getDeck() as $card) {
            array_push($stringDeck, $card->getAsWords());
        }
        $oneCard = $stringDeck[$cardNum];

        //remove the card just picked from the deck
        $deck->removeCard($cardNum);
        $amountCards = count($deck->getDeck());

        //Set the new count of the cards in the session
        $session->set("api_cards_left", $amountCards);
        $data = [
            'aCard' => $oneCard,
            'cardCount' => $session->get("api_cards_left")
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw", name: "api_card", methods:['POST'])]
    public function drawCard(
        int $number,
        SessionInterface $session
    ): Response {
        //retrive the count of cards left from the session
        if ($session->get("api_deck")) {
            $deck = $session->get("api_deck");
            $cardsLeft = $session->get("api_cards_left");
        } else {
            $deck = new DeckOfCards();
            $session->set("api_deck", $deck);
            $session->set("api_cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("api_cards_left");
        }
        // Throw exception if amount of cards to draw exceeds the deck
        if ($number > $cardsLeft) {
            throw new \Exception("Can not draw more cards than there are in the deck!");
        }

        $theCards = [];
        for ($i = 0; $i < $number; $i++) {
            $cardsLeft = $session->get("api_cards_left");
            $cardNum = random_int(0, $cardsLeft - 1);
            $stringDeck = [];

            foreach ($deck->getDeck() as $card) {
                array_push($stringDeck, $card->getAsWords());
            }
            $oneCard = $stringDeck[$cardNum];
            array_push($theCards, $oneCard);
            //remove the card just picked from the deck
            $deck->removeCard($cardNum);
            $amountCards = count($deck->getDeck());
            $session->set("api_cards_left", $amountCards);
        }
        // var_dump($deck);
        $data = [
            'theCards' => $theCards,
            'cardCount' => $session->get("api_cards_left")
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }

    #[Route("/api/deck/draw/{number<\d+>}", name: "api_several_cards", methods:['POST'])]
    public function drawSeveral(
        int $number,
        SessionInterface $session
    ): Response {
        //retrive the count of cards left from the session
        if ($session->get("api_deck")) {
            $deck = $session->get("api_deck");
            $cardsLeft = $session->get("api_cards_left");
        } else {
            $deck = new DeckOfCards();
            $session->set("api_deck", $deck);
            $session->set("api_cards_left", count($deck->getDeck()));
            $cardsLeft = $session->get("api_cards_left");
        }
        // Throw exception if amount of cards to draw exceeds the deck
        if ($number > $cardsLeft) {
            throw new \Exception("Can not draw more cards than there are in the deck!");
        }

        $theCards = [];
        for ($i = 0; $i < $number; $i++) {
            $cardsLeft = $session->get("api_cards_left");
            $cardNum = random_int(0, $cardsLeft - 1);
            $stringDeck = [];

            foreach ($deck->getDeck() as $card) {
                array_push($stringDeck, $card->getAsWords());
            }
            $oneCard = $stringDeck[$cardNum];
            array_push($theCards, $oneCard);
            //remove the card just picked from the deck
            $deck->removeCard($cardNum);
            $amountCards = count($deck->getDeck());
            $session->set("api_cards_left", $amountCards);
        }
        // var_dump($deck);
        $data = [
            'theCards' => $theCards,
            'cardCount' => $session->get("api_cards_left")
        ];

        $response = new JsonResponse($data);
        $response->setEncodingOptions(
            $response->getEncodingOptions() | JSON_PRETTY_PRINT
        );
        return $response;
    }
}
