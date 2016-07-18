<?php

namespace Persistence\Validator;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Se lanza cuando se intenta crear un proveedor inválido
 */
class InvalidProviderException extends BadRequestHttpException
{
    /**
     * @var array
     */
    public $groups;

    /**
     * @param array $groups
     */
    public function __construct($groups)
    {
        $this->groups = $groups;

        parent::__construct('Proveedor inválido');
    }
}
