<?php


namespace App\Entity\Index;


class Term implements \JsonSerializable
{
    const NAME      = 'term';
    const LIST_NAME = 'terms';

    protected Attribute $value;
    protected array     $competitions = [];

    /**
     * Term constructor.
     *
     * @param $value
     * @param $competitions
     */
    public function __construct(Attribute $value, array $competitions = [])
    {
        $this->value        = $value;
        $this->competitions = $competitions;
    }

    /**
     * @return Attribute
     */
    public function getValue() : Attribute
    {
        return $this->value;
    }

    /**
     * @param Attribute $value
     */
    public function setValue(Attribute $value) : void
    {
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function getCompetitions() : array
    {
        return $this->competitions;
    }

    /**
     * @param array $competitions
     */
    public function setCompetitions(array $competitions) : void
    {
        $this->competitions = $competitions;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            $this->getValue()->getName() => $this->getValue()->getValue(),
            'competitions'               => $this->getCompetitions(),
        ];
    }
}