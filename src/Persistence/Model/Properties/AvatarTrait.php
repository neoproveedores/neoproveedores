<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Persistence\Model\Avatar;

/**
 * Reistra una imagen.
 */
trait AvatarTrait
{
    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\Avatar")
     */
    protected $avatar;

    /**
     * @param string $avatar
     * @return self
     */
    public function setAvatar(Avatar $avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * @return string
     */
    public function getAvatarUrl()
    {
        if ($this->getAvatar() and $this->getAvatar()->getPath()) {
            return '/uploads/'.$this->getAvatar()->getFileName();
        }
    }

    /**
     * @param string $size
     *
     * @return string
     */
    public function getAvatarImage($size = '')
    {
        if ($url = $this->getAvatarUrl()) {
            return sprintf('<img src="%s" class="ui %s avatar">', $url, $size);
        }

        $icon = '<div class="ui %s avatar"><i class="user icon"></i></div>';

        return sprintf($icon, $size);
    }
}
