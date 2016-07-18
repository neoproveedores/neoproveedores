<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Registra palabras clave para su búsqueda posterior
 */
trait KeywordsTrait
{
    /**
     * @MongoDB\Collection
     * @MongoDB\Index
     */
    protected $keywords = [];

    /**
     * @param Collection $keywords
     * @return self
     */
    public function setKeywords(array $keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getKeywords()
    {
        return $this->keywords;
    }
}
