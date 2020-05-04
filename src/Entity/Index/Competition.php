<?php


namespace App\Entity\Index;


class Competition implements \JsonSerializable
{
    const NAME      = 'competition';
    const LIST_NAME = 'competitions';

    protected Attribute  $id;
    protected Attribute  $count;
    protected ?Attribute $tf_idf = null;

    /**
     * Document constructor.
     *
     * @param Attribute      $id
     * @param Attribute      $count
     * @param Attribute|null $tf_idf
     */
    public function __construct(Attribute $id, Attribute $count, Attribute $tf_idf = null)
    {
        $this->id     = $id;
        $this->count  = $count;
        $this->tf_idf = $tf_idf ?? new Attribute('tf-idf', (float) 0);
    }

    /**
     * @return Attribute
     */
    public function getId() : Attribute
    {
        return $this->id;
    }

    /**
     * @param Attribute $id
     */
    public function setId(Attribute $id) : void
    {
        $this->id = $id;
    }

    /**
     * @return Attribute
     */
    public function getCount() : Attribute
    {
        return $this->count;
    }

    /**
     * @param Attribute $count
     */
    public function setCount(Attribute $count) : void
    {
        $this->count = $count;
    }

    /**
     * @return Attribute|null
     */
    public function getTfIdf() : ?Attribute
    {
        return $this->tf_idf;
    }

    /**
     * @param Attribute|null $tf_idf
     */
    public function setTfIdf(?Attribute $tf_idf) : void
    {
        $this->tf_idf = $tf_idf;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize()
    {
        return [
            $this->getId()->getName()    => $this->getId()->getValue(),
            $this->getCount()->getName() => $this->getCount()->getValue(),
            $this->getTfIdf()->getName() => $this->getTfIdf()->getValue(),
        ];
    }
}