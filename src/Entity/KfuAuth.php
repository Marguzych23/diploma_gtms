<?php


namespace App\Entity;


class KfuAuth
{
    private ?string $accessToken;
    private ?string $refreshToken;
    private ?int    $expiresIn;
    private ?string $token_type;
    private ?string $scope;
    private array   $userData = [];

    /**
     * KfuAuth constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return string|null
     */
    public function getAccessToken() : ?string
    {
        return $this->accessToken;
    }

    /**
     * @param string|null $accessToken
     */
    public function setAccessToken(?string $accessToken) : void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string|null
     */
    public function getRefreshToken() : ?string
    {
        return $this->refreshToken;
    }

    /**
     * @param string|null $refreshToken
     */
    public function setRefreshToken(?string $refreshToken) : void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return int|null
     */
    public function getExpiresIn() : ?int
    {
        return $this->expiresIn;
    }

    /**
     * @param int|null $expiresIn
     */
    public function setExpiresIn(?int $expiresIn) : void
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return string|null
     */
    public function getTokenType() : ?string
    {
        return $this->token_type;
    }

    /**
     * @param string|null $token_type
     */
    public function setTokenType(?string $token_type) : void
    {
        $this->token_type = $token_type;
    }

    /**
     * @return string|null
     */
    public function getScope() : ?string
    {
        return $this->scope;
    }

    /**
     * @param string|null $scope
     */
    public function setScope(?string $scope) : void
    {
        $this->scope = $scope;
    }

    /**
     * @return array
     */
    public function getUserData() : array
    {
        return $this->userData;
    }

    /**
     * @param array $userData
     */
    public function setUserData(array $userData) : void
    {
        $this->userData = $userData;
    }
}