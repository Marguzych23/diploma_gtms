<?php


namespace App\Controller;


use App\Service\KfuAuthService;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Throwable;

class AuthController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     *
     * @return Response
     */
    public function login() : Response
    {
//        return $this->redirect(KfuAuthService::getCodeForLogin(   ));
        return $this->json(KfuAuthService::getCodeForLogin());
    }

    /**
     * @Route("/auth", name="app_auth")
     *
     * @param Request     $request
     *
     * @param UserService $userService
     *
     * @return Response
     */
    public function auth(
        Request $request, UserService $userService
    ) : Response {
        $code = $request->get('code');

        try {
            $userService->auth($code);
        } catch (Throwable $e) {
            return $this->redirectToRoute('competitions');
        }


//        return $this->redirect(KfuAuthService::getCodeForLogin());
        return $this->json([]);
    }
}