<?php


namespace App\Controller;

use App\Service\CompetitionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CompetitionController extends AbstractController
{
    /**
     * @Route("/competitions/get", name="get_competitions")
     * @param Request            $request
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

//        try {
            $query         = $request->get('query', '');
            $deadlineStart = $request->get('deadline_start', time());
            $deadlineEnd   = $request->get('deadline_end', 0);
            $industry      = $request->get('industry', [9, 10]);

            $data = $competitionService->getCompetitions($query, $deadlineStart, $deadlineEnd, $industry);
//        } catch (\Throwable $throwable) {
//            $message = $throwable->getMessage();
//        }

        return $this->json([
            'message' => $message,
            'data'    => $data,
        ]);
    }
}