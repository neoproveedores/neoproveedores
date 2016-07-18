<?php

namespace AppBundle\SearchEngine\Indexer;

use Component\SearchEngine\Indexer\AbstractDocumentIndexer;
use Component\SearchEngine\Indexer\KeywordExtractor;
use Persistence\Model\Provider;

/**
 * Indexador de proveedores
 */
class ProviderIndexer extends AbstractDocumentIndexer
{
    /**
     * Genera las palabras clave de un proveedor
     *
     * @param Provider $provider
     */
    public static function ensureKeywords(Provider $provider)
    {
        $skills = $provider->getSkills();
        $contact = $provider->getContact();

        $keys = KeywordExtractor::extract($contact->getBusinessName());
        $keys = KeywordExtractor::merge($contact->getFirstName(), $keys);
        $keys = KeywordExtractor::merge($contact->getLastName(), $keys);

        if (is_object($skills) || is_array($skills)) {
            foreach ($skills as $skill) {
                $ability = $skill->getAbility();
                $keys = KeywordExtractor::merge($ability->getName(), $keys);
            }
        }

        $provider->setKeywords($keys);
    }
}
