<?php


namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndustryRepository")
 */
class Industry
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $name;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\Competition", inversedBy="industry", cascade={"persist"})
     */
    private Collection $competitions;

    /**
     * @var Collection
     *
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="industry", cascade={"persist"})
     */
    private Collection $users;

    /**
     * Industry constructor.
     */
    public function __construct()
    {
        $this->competitions = new ArrayCollection();
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
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name) : void
    {
        $this->name = $name;
    }

    /**
     * @return Collection
     */
    public function getCompetitions()
    {
        return $this->competitions;
    }

    /**
     * @param Collection $competitions
     *
     * @return Industry
     */
    public function setCompetitions($competitions) : self
    {
        $this->competitions = $competitions;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getUsers() : Collection
    {
        return $this->users;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users) : void
    {
        $this->users = $users;
    }

    /**
     * @param Competition $competition
     *
     * @return Industry
     */
    public function addCompetition(Competition $competition) : self
    {
        if (!$this->competitions->contains($competition)) {
            $this->competitions[] = $competition;
            $competition->addIndustry($this);
        }

        return $this;
    }
}