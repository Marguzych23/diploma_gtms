<?php


namespace App\Controller;

use App\Service\CompetitionService;
use App\Service\IndustryService;
use DateTime;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class CompetitionController extends AbstractController
{
    /**
     * @Route("/competitions", name="search_competitions")
     * @param Request            $request
     * @param CompetitionService $competitionService
     *
     * @param IndustryService    $industryService
     *
     * @return Response
     */
    public function searchCompetitions(
        Request $request,
        CompetitionService $competitionService,
        IndustryService $industryService
    ) : Response {
        $message      = 'OK';
        $competitions = [];
        $industries   = [];
        $_request     = [];

        try {
            $query    = $request->get('query', '');
            $date     = $request->get('date', 0);
            $industry = $request->get('industry', []);

            if (($strpos = strpos($date, ' - ')) !== false) {
                $deadlineStart = strtotime(substr($date, 0, $strpos));
                $deadlineEnd   = strtotime(substr($date, $strpos + 3));
            } else {
                $deadlineStart = strtotime($date);
                $deadlineEnd   = strtotime($date) + 86399;
            }

            $competitions = $competitionService->getCompetitions($query, $deadlineStart, $deadlineEnd, $industry);
            $industries   = $industryService->getAll();

            $_request = [
                'query'    => $query,
                'date'     => $date,
                'industry' => $industry,
            ];
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }

        return $this->render(
            'competition/index.html.twig',
            [
                'message'      => $message,
                'competitions' => $competitions,
                'industries'   => $industries,
                'datetime'     => new DateTime(),
                'request'      => $_request,
            ]
        );
    }

    /**
     * @Route("/competition", name="get_competition")
     * @param Request            $request
     * @param CompetitionService $competitionService
     *
     * @return Response
     */
    public function getCompetition(
        Request $request,
        CompetitionService $competitionService
    ) : Response {
        $message     = 'OK';
        $competition = null;

        try {
            var_dump($_REQUEST);
            exit();

            $id = (int) $request->get('id', 0);

            $competition = $competitionService->getCompetitionById($id);
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }

        return $this->render(
            'competition/data.html.twig',
            [
                'message'     => $message,
                'competition' => $competition,
            ]
        );
    }

    /**
     * @Route("/competitions/get", name="get_competitions")
     * @param Request            $request
     * @param CompetitionService $competitionService
     *
     * @return Response
     */
    public function getCompetitions(
        Request $request,
        CompetitionService $competitionService
    ) : Response {
        $message = 'OK';
        $data    = [];

        try {
            $query         = $request->get('query', '');
            $deadlineStart = $request->get('deadline_start', time());
            $deadlineEnd   = $request->get('deadline_end', 0);
            $industry      = $request->get('industry', []);

            $data = $competitionService->getCompetitions($query, $deadlineStart, $deadlineEnd, $industry);
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }

        return $this->json([
            'message' => $message,
            'data'    => $data,
        ]);
    }
}