<?php

namespace Persistence\Repository;

use Doctrine\ODM\MongoDB\DocumentRepository;
use Persistence\Model\DocumentInterface;

/**
 * Repositorio base para documentos de MongoDB.
 */
abstract class AbstractDocumentRepository extends DocumentRepository
{
    /**
     * Guarda un documento.
     * @param Persistence\Repository\Model\DocumentInterface $document
     * @return self
     */
    public function save(DocumentInterface $document)
    {
        $this->dm->persist($document);
        $this->dm->flush();

        return $this;
    }
}
