<?php

namespace Persistence\Model;

/**
 * Interfaz para documentos de MongoDB.
 */
interface DocumentInterface
{
    /**
     * @return string
     */
    public function getId();
}
