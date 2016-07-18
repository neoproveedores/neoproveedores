<?php

namespace Persistence\Model\Properties;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\ODM\MongoDB\PersistentCollection;

/**
 * Registra una colección de ficheros.
 */
trait FilesTrait
{
    /**
     * @MongoDB\EmbedMany(targetDocument="Persistence\Model\File")
     */
    protected $files;

    /**
     * @param array $files
     *
     * @return self
     */
    public function setFiles($files)
    {
        $this->files = $files;

        return $this;
    }

    /**
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param int $index
     *
     * @return File
     */
    public function getFile($index)
    {
        return $this->files->get($index);
    }

    /**
     * Limpia los ficheros vacíos que generan los formularios sin rellenar.
     */
    public function clearEmptyFiles()
    {
        foreach ($this->files as $index => $file) {
            if (! $file || ! $file->getName()) {
                if ($this->files instanceof PersistentCollection) {
                    $this->files->remove($index);
                } else {
                    unset($this->files[$index]);
                }
            }
        }
    }
}
