<?php

namespace AppBundle\SearchEngine\Indexer;

use Component\SearchEngine\Indexer\AbstractDocumentIndexer;
use Component\SearchEngine\Indexer\KeywordExtractor;
use Persistence\Model\Project;

/**
 * Indexador de proyectos
 */
class ProjectIndexer extends AbstractDocumentIndexer
{
    /**
     * Genera las palabras clave de un proyecto
     *
     * @param Project $project
     */
    public static function ensureKeywords(Project $project)
    {
        $abilities = $project->getAbilities();
        $keys = KeywordExtractor::extract($project->getName());

        if (is_object($abilities) || is_array($abilities)) {
            foreach ($abilities as $ability) {
                $keys = KeywordExtractor::merge($ability->getName(), $keys);
            }
        }

        $project->setKeywords($keys);
    }
}
