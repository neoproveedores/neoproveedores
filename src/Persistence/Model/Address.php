<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Dirección.
 *
 * @MongoDB\EmbeddedDocument
 */
class Address
{
    /**
     * @MongoDB\String
     */
    protected $street;

    /**
     * @MongoDB\String
     */
    protected $city;

    /**
     * @MongoDB\String
     */
    protected $region;

    /**
     * @MongoDB\String
     */
    protected $postalCode;

    /**
     * @MongoDB\String
     */
    protected $country;

    /**
     * @param string $street
     * @param string $city
     * @param string $region
     * @param string $postal
     * @param string $country
     */
    public function __construct($street = null, $city = null, $region = null, $postal = null, $country = null)
    {
        $this->setStreet($street);
        $this->setCity($city);
        $this->setRegion($region);
        $this->setPostalCode($postal);
        $this->setCountry($country);
    }

    /**
     * @param string $street
     * @return self
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @param string $postalCode
     * @return self
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param string $city
     * @return self
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * @param string $region
     * @return self
     */
    public function setRegion($region)
    {
        $this->region = $region;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegion()
    {
        return $this->region;
    }

    /**
     * @param string $country
     * @return self
     */
    public function setCountry($country)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return string
     */
    public function getCountry()
    {
        return $this->country;
    }
}
