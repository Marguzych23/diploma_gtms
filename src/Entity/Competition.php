<?php


namespace App\Entity;


use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CompetitionRepository")
 */
class Competition implements JsonSerializable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $name = null;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Industry", mappedBy="competitions", cascade={"persist"})
     * @ORM\JoinTable(name="competitions_industries")
     */
    private Collection $industries;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $deadline = null;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private ?string $grantSize = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private ?string $url = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private ?DateTime $updateDate = null;

    /**
     * Competition constructor.
     */
    public function __construct()
    {
        $this->industries = new ArrayCollection();
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
     * @return string|null
     */
    public function getName() : ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     */
    public function setName(?string $name) : void
    {
        $this->name = $name;
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
     *
     * @return Competition
     */
    public function setIndustries(Collection $industries) : self
    {
        $this->industries = $industries;

        return $this;
    }

    /**
     * @param Industry $industry
     *
     * @return Competition
     */
    public function addIndustry(Industry $industry) : self
    {
        if (!$this->industries->contains($industry)) {
            $this->industries[] = $industry;
            $industry->addCompetition($this);
        }

        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDeadline() : ?DateTime
    {
        return $this->deadline;
    }

    /**
     * @param DateTime|null $deadline
     */
    public function setDeadline(?DateTime $deadline) : void
    {
        $this->deadline = $deadline;
    }

    /**
     * @return string|null
     */
    public function getGrantSize() : ?string
    {
        return $this->grantSize;
    }

    /**
     * @param string|null $grantSize
     */
    public function setGrantSize(?string $grantSize) : void
    {
        $this->grantSize = $grantSize;
    }

    /**
     * @return string|null
     */
    public function getUrl() : ?string
    {
        return $this->url;
    }

    /**
     * @param string|null $url
     */
    public function setUrl(?string $url) : void
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            'name'       => $this->getName(),
            'grant_size' => $this->getGrantSize(),
            'url'        => $this->getUrl(),
            'deadline'   => $this->getDeadline(),
        ];
    }

    /**
     * @return DateTime|null
     */
    public function getUpdateDate() : ?DateTime
    {
        return $this->updateDate;
    }

    /**
     * @param DateTime|null $updateDate
     */
    public function setUpdateDate(?DateTime $updateDate = null) : void
    {
        try {
            // WILL be saved in the database
            $this->updateDate = $updateDate ?? new \DateTime("now");
        } catch (\Throwable $exception) {
        }
    }
}