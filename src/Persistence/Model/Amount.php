<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Importe de un bien o servicio.
 *
 * @MongoDB\EmbeddedDocument
 */
class Amount
{
    /**
     * @MongoDB\Float
     * @MongoDB\Index
     */
    protected $value;

    /**
     * @MongoDB\String
     */
    protected $currency;

    /**
     * @param float  $value
     * @param string $currency
     */
    public function __construct($value = 0, $currency = Currency::EUR)
    {
        $this->setValue($value);
        $this->setCurrency($currency);
    }

    /**
     * @param float $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $currency
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
