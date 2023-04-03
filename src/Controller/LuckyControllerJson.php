<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class LuckyControllerJson
{
    #[Route("/api/quote")]
    public function jsonNumber(): Response
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
}