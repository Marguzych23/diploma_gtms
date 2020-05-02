<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{

    /**
     * @Route("/", name="index")
     * @param Request $request
     *
     * @return Response
     */
    public function index(
        Request $request
    ) : Response {
        $message      = 'OK';
        $competitions = [];

        return $this->json([
            'competitions' => $competitions,
            'message'      => $message,
        ]);
    }


}