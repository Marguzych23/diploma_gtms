<?php


namespace App\Controller;

use App\Service\CompetitionService;
use App\Service\IndustryService;
use App\Service\UserService;
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
     * @param UserService        $userService
     *
     * @return Response
     */
    public function searchCompetitions(
        Request $request,
        CompetitionService $competitionService,
        IndustryService $industryService,
        UserService $userService
    ) : Response {
        $message      = 'OK';
        $competitions = [];
        $industries   = [];

        $query    = $request->get('query', '');
        $date     = $request->get('date', '');
        $industry = $request->get('industry', []);

        try {
            if (($strpos = strpos($date, ' - ')) !== false) {
                $deadlineStart = strtotime(substr($date, 0, $strpos));
                $deadlineEnd   = strtotime(substr($date, $strpos + 3));
            } elseif (strlen($date) > 0) {
                $deadlineStart = strtotime($date);
                $deadlineEnd   = strtotime($date) + 86399;
            } else {
                $deadlineStart = 0;
                $deadlineEnd   = 0;
            }

            $competitions = $competitionService->getCompetitions($query, $deadlineStart, $deadlineEnd, $industry);
            $industries   = $industryService->getAll();
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }
        $_request = [
            'query'    => $query,
            'date'     => $date,
            'industry' => $industry,
        ];

        return $this->render(
            'competition/index.html.twig',
            [
                'message'      => $message,
                'competitions' => $competitions,
                'industries'   => $industries,
                'datetime'     => new DateTime(),
                'request'      => $_request,
                'user'         => UserService::getUser() !== null
                    ? $userService->getUserByEmail(
                        UserService::getUser()->getEmail()
                    )
                    : null,
            ]
        );
    }

    /**
     * @Route("/competition", name="get_competition")
     * @param Request            $request
     * @param CompetitionService $competitionService
     *
     * @param UserService        $userService
     *
     * @return Response
     */
    public function getCompetition(
        Request $request,
        CompetitionService $competitionService,
        UserService $userService
    ) : Response {
        $message     = 'OK';
        $competition = null;
        $page        = 0;

        try {
            $id   = (int) $request->get('id', 0);
            $page = (int) $request->get('page', 0);

            $competition = $competitionService->getCompetitionById($id);
            $userService->deleteNotifyCompetition($competition);
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }

        return $this->render(
            'competition/data.html.twig',
            [
                'message'     => $message,
                'competition' => $competition,
                'page'        => $page,
                'user'        => UserService::getUser() !== null
                    ? $userService->getUserByEmail(
                        UserService::getUser()->getEmail()
                    )
                    : null,
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