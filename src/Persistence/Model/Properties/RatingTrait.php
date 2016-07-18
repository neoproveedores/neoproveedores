<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Clasificación del 0 al 5
 */
trait RatingTrait
{
    /**
     * @MongoDB\Int
     */
    protected $rating;

    /**
     * @param integer $rating
     * @return self
     */
    public function setRating($rating)
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return integer
     */
    public function getRating()
    {
        return $this->rating;
    }
}
