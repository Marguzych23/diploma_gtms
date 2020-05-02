<?php


namespace App\Controller;


use Symfony\Component\DependencyInjection\EnvVarProcessor;
use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class IndexController extends AbstractController
{

    /**
     * @Route("/", name="index")
     * @param Request         $request
     *
     * @param EnvVarProcessor $envVarProcessor
     *
     * @return Response
     */
    public function index(
        Request $request,
        EnvVarProcessor $envVarProcessor
    ) : Response {
        $message      = 'OK';
        $competitions = [];

        return $this->json([
            'competitions' => $competitions,
            'message'      => $message,
        ]);
    }


}