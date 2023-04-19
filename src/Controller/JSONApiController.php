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

class JSONApiController extends AbstractController
{
    #[Route("api", name: "api")]
    public function cards(): Response
    {
        return $this->render('api.html.twig');
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

    #[Route("/api/shuffle", name:"api_shuffle", methods:['POST'])]
    public function shuffle(): Response
    {
        $deck = new DeckOfCards();
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
}
