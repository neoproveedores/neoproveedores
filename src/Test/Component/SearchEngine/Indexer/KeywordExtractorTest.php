<?php

namespace Test\Component\SearchEngine\Indexer;

use phpunit\framework\TestCase;
use Component\SearchEngine\Indexer\KeywordExtractor;
use Behat\Transliterator\Transliterator;

/**
 * Pruebas para KeywordExtractor
 */
class KeywordExtractorTest extends TestCase
{
    public function testTransliterate()
    {
        $text = '¡Diseño de interacción para una web!';
        $text = Transliterator::transliterate($text, ' ');

        $this->assertEquals($text, 'diseno de interaccion para una web');
    }

    public function testUTF8Keywords()
    {
        $keys = KeywordExtractor::extract('¡Diseño gratis día y noche!');

        foreach ($keys as $key) {
            $this->assertTrue($this->isUTF8String($key));
        }
    }

    protected function isUTF8String($string)
    {
        return (boolean) preg_match('!!u', $string);
    }
}
