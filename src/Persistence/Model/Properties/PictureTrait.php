<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\File;

/**
 * Reistra una imagen.
 */
trait PictureTrait
{
    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\File")
     */
    protected $picture;

    /**
     * @param string $picture
     * @return self
     */
    public function setPicture(File $picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }
}
