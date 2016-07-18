<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Presupuesto para un proyecto.
 *
 * @MongoDB\EmbeddedDocument
 */
class Budget
{
    use Properties\NotesTrait;
    use Properties\FilesTrait;
    use Properties\TimingTrait;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Amount")
     */
    protected $amount;

    /**
     * @param null|Amount $amount
     * @return self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * @return Persistence\Model\Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }
}
