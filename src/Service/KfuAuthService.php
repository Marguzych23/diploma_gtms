<?php


namespace App\Service;


use App\Entity\KfuAuth;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class KfuAuthService
{
    const GET_TOKEN_PATH           = '/oauth/token/';
    const DELETE_ACCESS_TOKEN_PATH = '/oauth/revoke_token/';
    const GET_PROFILE_PATH         = '/api/v1.0/profile/';

    /**
     * @param KfuAuth $kfuAuth
     *
     * @return KfuAuth
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function getUserData(KfuAuth $kfuAuth)
    {
        $client = HttpClient::create();

        $kfuAuth->setUserData(
            $client->request('GET',
                self::getOauthURL() . self::GET_PROFILE_PATH,
                [
                    'headers' => [
                        'Authorization' => $kfuAuth->getTokenType() . ' ' . $kfuAuth->getAccessToken(),
                    ],
                ]
            )->toArray()
        );

        return $kfuAuth;
    }

    /**
     * @param string $authCode
     *
     * @return KfuAuth|void
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function getUserTokens(string $authCode)
    {
        $client = HttpClient::create();

        $response = $client->request('POST',
            self::getOauthURL() . self::GET_TOKEN_PATH,
            [
                'body' => [
                    'client_id'     => self::getClientId(),
                    'client_secret' => self::getClientSecret(),
                    'grant_type'    => $authCode,
                    'code'          => $authCode,
                    'redirect_uri'  => self::getKfuFirstCallbackURL(),
                ],
            ]
        )->toArray();

        $kfuAuth = new KfuAuth();
        $kfuAuth->setAccessToken($response['access_token']);
        $kfuAuth->setRefreshToken($response['refresh_token']);
        $kfuAuth->setExpiresIn($response['expires_in']);
        $kfuAuth->setTokenType($response['token_type']);
        $kfuAuth->setScope($response['scope']);

        return $kfuAuth;
    }

    /**
     * @param KfuAuth $kfuAuth
     *
     * @return KfuAuth
     * @throws ClientExceptionInterface
     * @throws DecodingExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     */
    public static function refreshUserTokens(KfuAuth $kfuAuth)
    {
        $client = HttpClient::create();

        $response = $client->request('POST',
            self::getOauthURL() . self::GET_TOKEN_PATH,
            [
                'body' => [
                    'client_id'     => self::getClientId(),
                    'client_secret' => self::getClientSecret(),
                    'grant_type'    => 'refresh_token',
                    'refresh_token' => $kfuAuth->getRefreshToken(),
                ],
            ]
        )->toArray();

        $kfuAuth->setAccessToken($response['access_token']);
        $kfuAuth->setRefreshToken($response['refresh_token']);
        $kfuAuth->setExpiresIn($response['expires_in']);
        $kfuAuth->setTokenType($response['token_type']);
        $kfuAuth->setScope($response['scope']);

        return $kfuAuth;
    }


    /**
     * @param KfuAuth $kfuAuth
     *
     * @return KfuAuth
     * @throws TransportExceptionInterface
     */
    public static function deleteUserAccessToken(KfuAuth $kfuAuth)
    {
        $response = HttpClient::create()->request('POST',
            self::getOauthURL() . self::DELETE_ACCESS_TOKEN_PATH,
            [
                'body' => [
                    'client_id'     => self::getClientId(),
                    'client_secret' => self::getClientSecret(),
                    'access_token'  => $kfuAuth->getRefreshToken(),
                ],
            ]
        );

        if ($response->getStatusCode() === 200) {
            $kfuAuth->setAccessToken(null);
        }

        return $kfuAuth;
    }

    /**
     * @return string
     */
    public static function getCodeForLogin()
    {
        return self::getOauthURL() . '/oauth/authorize?response_type=code'
            . '&client_id=' . self::getClientId()
            . '&redirect_uri=' . self::getKfuFirstCallbackURL();
    }

    /**
     * @return string
     */
    protected static function getClientId()
    {
        return $_ENV['KFU_CLIENT_ID'];
    }

    /**
     * @return string
     */
    protected static function getClientSecret()
    {
        return $_ENV['KFU_CLIENT_SECRET'];
    }

    /**
     * @return string
     */
    protected static function getOauthURL()
    {
        return $_ENV['KFU_OAUTH_URL'];
    }

    /**
     * @return string
     */
    protected static function getKfuFirstCallbackURL()
    {
        return $_ENV['HOST'] . $_ENV['KFU_FIRST_CALLBACK_URL'];
    }
}