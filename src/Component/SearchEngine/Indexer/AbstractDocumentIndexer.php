<?php

namespace Component\SearchEngine\Indexer;

use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\DocumentRepository;

/**
 * Indexador de palabras clave para documentos
 */
abstract class AbstractDocumentIndexer
{
    /**
     * @var Doctrine\ODM\MongoDB\DocumentManager
     */
    protected $dm;

    /**
     * @var Doctrine\ODM\MongoDB\DocumentRepository
     */
    protected $repository;

    /**
     * @param DocumentManager    $dm
     * @param DocumentRepository $repository
     */
    public function __construct(
        DocumentManager $dm,
        DocumentRepository $repository
    ) {
        $this->dm = $dm;
        $this->repository = $repository;
    }

    /**
     * Indexa todas las obras
     *
     * @return int Número de obras indexadas
     */
    public function indexAll()
    {
        $documents = $this->repository->findAll();

        foreach ($documents as $document) {
            $this->ensureKeywords($document);
            $this->dm->persist($document);
        }
        $this->dm->flush();

        return count($documents);
    }
}
