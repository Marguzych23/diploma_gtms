<?php


namespace App\Controller;

use App\Service\CompetitionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="app_user_profile")
     * @param Request            $request
     * @param CompetitionService $competitionService
     *
     * @return Response
     */
    public function index(
        Request $request,
        CompetitionService $competitionService
    ) : Response {
        $message      = 'OK';
        $competitions = [];

        try {
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }
        return $this->render(
            'profile/index.html.twig',
            [
                'message'      => $message,
                'competitions' => $competitions,
            ]
        );
    }

}