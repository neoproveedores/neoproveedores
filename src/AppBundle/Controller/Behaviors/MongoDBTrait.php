<?php

namespace AppBundle\Controller\Behaviors;

/**
 * Facilita el trabajo con MongoDB
 */
trait MongoDBTrait
{
    /**
     * @param string $document
     * @return DocumentRepository
     */
    protected function getRepository($document)
    {
        return $this->get(sprintf('persistence.%s_repository', $document));
    }

    /**
     * @param mixed $current
     */
    protected function saveDocument($document)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $dm->persist($document);
        $dm->flush();
    }

    /**
     * @param mixed $current
     */
    protected function removeDocument($document)
    {
        $dm = $this->get('doctrine.odm.mongodb.document_manager');

        $dm->remove($document);
        $dm->flush();
    }
}
