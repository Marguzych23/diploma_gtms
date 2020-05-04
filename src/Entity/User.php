<?php

namespace App\Entity;


use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private ?string $email = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Industry", mappedBy="users", cascade={"persist"})
     * @ORM\JoinTable(name="users_industries")
     */
    private Collection $industries;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?\DateTime $lastNotifyDate = null;

    /**
     * User constructor.
     */
    public function __construct()
    {
        $this->industries = new  ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getEmail() : string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email) : void
    {
        $this->email = $email;
    }

    /**
     * @return Collection
     */
    public function getIndustries() : Collection
    {
        return $this->industries;
    }

    /**
     * @param Collection $industries
     */
    public function setIndustries(Collection $industries) : void
    {
        $this->industries = $industries;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastNotifyDate() : ?\DateTime
    {
        return $this->lastNotifyDate;
    }

    /**
     * @param \DateTime|null $lastNotifyDate
     */
    public function setLastNotifyDate(?\DateTime $lastNotifyDate) : void
    {
        $this->lastNotifyDate = $lastNotifyDate;
    }
}