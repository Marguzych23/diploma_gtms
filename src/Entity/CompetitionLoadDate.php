<?php


namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetitionLoadDateRepository")
 */
class CompetitionLoadDate
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private int $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private ?DateTime $date;

    /**
     * Default 0 - is not actual
     * 1 - is actual date
     *
     * @ORM\Column(type="integer", options={"default" : 0})
     */
    private int $status = 0;

    /**
     * CompetitionLoadDate constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $this->date = new DateTime();
    }

    /**
     * @return int
     */
    public function getId() : int
    {
        return $this->id;
    }

    /**
     * @return DateTime|null
     */
    public function getDate() : ?DateTime
    {
        return $this->date;
    }

    /**
     * @return int
     */
    public function getStatus() : int
    {
        return $this->status;
    }
}