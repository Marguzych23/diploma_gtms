<?php


namespace App\Controller;

use App\Service\CompetitionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;


class AdminController extends AbstractController
{

    /**
     * @Route("/admin/competitions/update", name="admin_competitions_update")
     * @param Request            $request
     *
     * @param CompetitionService $competitionService
     *
     * @return Response
     */
    public function index(
        Request $request,
        CompetitionService $competitionService
    ) : Response {
        $message = 'OK';
        $data    = [];

        try {
            $competitionService->updateDataFromPASC();
        } catch (Throwable $e) {
            $message = $e->getMessage();
//            $data    = [
//                'line'  => $e->getLine(),
//                'trace' => $e->getTrace(),
//            ];
        }

        return $this->json([
            'message' => $message,
            'data'    => $data,
        ]);
    }

}