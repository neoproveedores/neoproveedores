<?php

namespace Component\SearchEngine\Indexer;

use Behat\Transliterator\Transliterator;

/**
 * Divide textos en palabras clave
 *
 * @version 1.1
 */
class KeywordExtractor
{
    /**
     * Extrae las palabras clave de un texto
     *
     * @param string $text
     * @return array
     */
    public static function extract($text)
    {
        $keywords = [];
        $text = Transliterator::transliterate($text, ' ');

        return array_filter(explode(' ', $text), function ($word) {
            return ! empty($word);
        });
    }

    /**
     * Mezcla las palabras clave de un texto con otras
     *
     * @param string $text
     * @param array  $keywords
     * @return array
     */
    public static function merge($text, $keywords)
    {
        return array_merge(self::extract($text), $keywords);
    }
}
