<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Modelo para información de contacto.
 *
 * @MongoDB\EmbeddedDocument
 */
class Contact
{
    use Properties\AvatarTrait;
    use Properties\PersonTrait;

    /**
     * @MongoDB\String
     * @MongoDB\Index
     */
    protected $businessName;

    /**
     * @MongoDB\String
     */
    protected $position;

    /**
     * @MongoDB\String
     */
    protected $email;

    /**
     * @MongoDB\String
     */
    protected $phone;

    /**
     * @MongoDB\String
     */
    protected $alternatePhone;

    /**
     * @MongoDB\String
     */
    protected $web;

    /**
     * @MongoDB\String
     */
    protected $twitter;

    /**
     * @MongoDB\String
     */
    protected $facebook;

    /**
     * @param string $name
     * @param string $last
     * @param string $position
     */
    public function __construct($name = null, $last = null, $position = null)
    {
        if ($name && $last) {
            $this->setFirstName($name);
            $this->setLastName($last);
            $this->setPosition($position);
        } else {
            $this->setBusinessName($name);
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        if ($this->getBusinessName()) {
            return $this->getBusinessName();
        }

        return join(' ', [$this->getFirstName(), $this->getLastName()]);
    }

    /**
     * @param string $businessName
     * @return self
     */
    public function setBusinessName($businessName)
    {
        $this->businessName = $businessName;

        return $this;
    }

    /**
     * @return string
     */
    public function getBusinessName()
    {
        return $this->businessName;
    }

    /**
     * @param string $position
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $email
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $phone [description]
     * @return self
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * @return string [description]
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param string $alternatePhone
     * @return self
     */
    public function setAlternatePhone($alternatePhone)
    {
        $this->alternatePhone = $alternatePhone;

        return $this;
    }

    /**
     * @return string
     */
    public function getAlternatePhone()
    {
        return $this->alternatePhone;
    }

    /**
     * @param string $web
     * @return self
     */
    public function setWeb($web)
    {
        $this->web = $web;

        return $this;
    }

    /**
     * @return string
     */
    public function getWeb()
    {
        return $this->web;
    }

    /**
     * @param string $twitter
     * @return self
     */
    public function setTwitter($twitter)
    {
        $this->twitter = $twitter;

        return $this;
    }

    /**
     * @return string
     */
    public function getTwitter()
    {
        return $this->twitter;
    }

    /**
     * @param string $facebook [description]
     * @return self
     */
    public function setFacebook($facebook)
    {
        $this->facebook = $facebook;

        return $this;
    }

    /**
     * @return string [description]
     */
    public function getFacebook()
    {
        return $this->facebook;
    }

    /**
     * @param ExecutionContextInterface $context
     */
    public function validatePerson(ExecutionContextInterface $context)
    {
        $message = (new NotBlank())->message;

        if ($context->getPropertyPath() != 'data.contact') {
            if (empty($this->getFirstName())) {
                $context->addViolationAt('firstName', $message);
            }

            if (empty($this->getLastName())) {
                $context->addViolationAt('lastName', $message);
            }
        }
    }
}
