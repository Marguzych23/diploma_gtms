<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractController extends \Symfony\Bundle\FrameworkBundle\Controller\AbstractController
{
    protected function json($data, int $status = 200, array $headers = [], array $context = []) : JsonResponse
    {
        return parent::json($data, $status, $headers,
            array_merge(
                [
                    'json_encode_options' => JSON_UNESCAPED_UNICODE,
                ],
                $context
            )
        );
    }
}