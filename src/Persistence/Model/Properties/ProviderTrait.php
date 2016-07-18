<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Registra un proveedor
 */
trait ProviderTrait
{
    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="Persistence\Model\Provider",
     *     cascade={"persist"}
     * )
     * @MongoDB\Index
     */
    protected $provider;

    /**
     * @param null|Provider $provider
     * @return self
     */
    public function setProvider($provider)
    {
        $this->provider = $provider;

        return $this;
    }

    /**
     * @return Persistence\Model\Provider
     */
    public function getProvider()
    {
        return $this->provider;
    }
}
