<?php


namespace App\Controller;

use App\Service\PascService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AdminController extends AbstractController
{

    /**
     * @Route("/admin/competitions/update", name="admin_competitions_update")
     * @param Request $request
     *
     * @return Response
     */
    public function index(
        Request $request
    ) : Response {
        $message      = 'OK';
        $competitions = [
            PascService::getToken(),
            PascService::getHost(),
        ];

        return $this->json([
            'competitions' => $competitions,
            'message'      => $message,
        ]);
    }

}