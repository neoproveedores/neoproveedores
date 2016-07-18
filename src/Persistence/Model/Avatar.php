<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Información sobre un avatar almacenado.
 *
 * @MongoDB\EmbeddedDocument
 */
class Avatar extends File
{
    /**
     * Devuelve la ruta para los avatares subidos.
     *
     * @return string
     */
    public function getUploadDirectory()
    {
        return __DIR__.'/../../../web/uploads';
    }
}
