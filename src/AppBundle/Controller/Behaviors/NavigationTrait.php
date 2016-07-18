<?php

namespace AppBundle\Controller\Behaviors;

use Persistence\Model\DocumentInterface;

/**
 * Facilita la navegación adelante y atrás
 */
trait NavigationTrait
{
    /**
     * Devuelve el documento anterior en un listado
     *
     * @param DocumentInterface $current
     * @param array             $documents
     *
     * @return DocumentInterface
     */
    protected function searchPrevious(DocumentInterface $current, $documents)
    {
        $previous = null;

        foreach ($documents as $document) {
            if ($document->getId() == $current->getId()) {
                return $previous;
            }
            $previous = $document;
        }
    }

    /**
     * @param mixed $current
     *
     * @return mixed
     */
    protected function searchNext($current, $documents)
    {
        $next = false;

        foreach ($documents as $document) {
            if ($next) {
                return $document;
            }
            if ($document->getId() == $current->getId()) {
                $next = true;
            }
            $previous = $document;
        }
    }
}
