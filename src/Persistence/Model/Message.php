<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Mensaje sobre un proyecto.
 *
 * @MongoDB\Document(
 *     collection="messages",
 *     repositoryClass="Persistence\Repository\MessageRepository"
 * )
 */
class Message implements DocumentInterface
{
    use Properties\AuthorTrait;
    use Properties\FilesTrait;
    use Properties\IdentityTrait;
    use Properties\ProjectTrait;
    use Properties\ProviderTrait;
    use Properties\TimestampTrait;

    /**
     * @MongoDB\String
     */
    protected $body;

    /**
     * @MongoDB\Collection
     */
    protected $readBy;

    /**
     * @param Project  $project
     * @param Provider $provider
     */
    public function __construct($project = null, $provider = null)
    {
        $this->setProject($project);
        $this->setProvider($provider);
    }

    /**
     * @param string $body
     *
     * @return self
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param array $readBy
     * @return self
     */
    public function setReadBy($readBy)
    {
        $this->readBy = $readBy;

        return $this;
    }

    /**
     * @return array
     */
    public function getReadBy()
    {
        return $this->readBy;
    }

    /**
     * @return string
     */
    public function getName()
    {
        if ($this->getAuthor()->getProvider() == $this->getProvider()) {
            return (string) $this->getProvider()->getContact();
        }

        return (string) $this->getAuthor()->getContact();
    }

    /**
     * @param string $size
     *
     * @return string
     */
    public function getAvatarImage($size = '')
    {
        if ($this->getAuthor()->getProvider() == $this->getProvider()) {
            return $this->getProvider()->getContact()->getAvatarImage($size);
        }

        return $this->getAuthor()->getContact()->getAvatarImage($size);
    }
}
