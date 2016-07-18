<?php

namespace Component\Twig;

/**
 * Muestra el tamaño de un fichero de forma legible
 */
class FileSizeExtension extends \Twig_Extension
{
    const UNIT = 1024;
    const PREFIXES = 'KMGTPE';

    /**
     * @return string
     */
    public function getName()
    {
        return 'file_size';
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            'file_size' => new \Twig_Filter_Method($this, 'getFileSize'),
        );
    }

    /**
     * @param int $bytes
     *
     * @return void
     */
    public function getFileSize($bytes)
    {
        $exp = intval((log($bytes) / log(self::UNIT)));
        $prefix = substr(self::PREFIXES, $exp - 1, 1);

        if ($bytes <= self::UNIT) {
            return $bytes.' B';
        }

        return sprintf('%.1f %sB', $bytes / pow(self::UNIT, $exp), $prefix);
    }
}
