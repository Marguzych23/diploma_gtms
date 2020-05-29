<?php


namespace App\Controller;

use App\Service\IndustryService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{
    /**
     * @Route("/profile", name="app_profile")
     * @param IndustryService $industryService
     * @param UserService     $userService
     *
     * @return Response
     */
    public function index(
        IndustryService $industryService,
        UserService $userService
    ) : Response {
        $message        = 'OK';
        $industries     = [];
        $competitions   = [];
        $competitionsId = [];
        $industriesId   = [];

        if (UserService::getUser() === null) {
            return $this->redirectToRoute('app_login');
        }

        try {
            $competitions   = $userService->getCompetitions();
            $industries     = $industryService->getAll();
            $competitionsId = $userService->getCompetitionsIdForNotify();
            $industriesId   = $userService->getIndustriesId();
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }

        return $this->render(
            'profile/index.html.twig',
            [
                'message'         => $message,
                'industries'      => $industries,
                'industries_id'   => $industriesId,
                'competitions'    => $competitions,
                'competitions_id' => $competitionsId,
                'datetime'        => new \DateTime(),
                'user'            => UserService::getUser() !== null
                    ? $userService->getUserByEmail(
                        UserService::getUser()->getEmail()
                    )
                    : null,
            ]
        );
    }

    /**
     * @Route("/profile/data/set", name="profile_data_set")
     * @param Request     $request
     * @param UserService $userService
     *
     * @return Response
     */
    public function setData(
        Request $request,
        UserService $userService
    ) : Response {
        $message = 'OK';

        try {
            $industries     = $request->get('industries', []);
            $emailSubscribe = (bool) $request->get('email_subscribe', 0);


            if ($emailSubscribe !== null) {
                $userService->emailSubscribe($emailSubscribe);
                $userService->emailSubscribeOnIndustryPASC();
            }

            if (is_array($industries)) {
                $ind = [];
                foreach ($industries as $industry) {
                    if (strlen($industry) > 0) {
                        $ind[] = (int) $industry;
                    }
                }
                $userService->notifyIndustries($ind);

                $userService->emailSubscribeOnIndustryPASC();
            }

            $this->redirectToRoute('app_profile');
        } catch (\Throwable $throwable) {
            $message = $throwable->getMessage();
        }

        return $this->json([
            'message' => $message,
        ]);
    }

    /**
     * @Route("/profile/data", name="user_data")
     * @return Response
     */
    public function getProfileData() : Response
    {
        var_dump(UserService::getUser());
        return $this->json('');
    }

}