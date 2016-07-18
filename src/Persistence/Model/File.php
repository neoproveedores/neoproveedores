<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Información sobre un fichero almacenado.
 *
 * @MongoDB\EmbeddedDocument
 */
class File
{
    use Properties\NameTrait;
    use Properties\TimestampTrait;

    /**
     * @MongoDB\String
     */
    protected $path;

    /**
     * @MongoDB\String
     */
    protected $mime;

    /**
     * @MongoDB\Int
     */
    protected $size;

    /**
     * @param string $path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $mime
     *
     * @return self
     */
    public function setMime($mime)
    {
        $this->mime = $mime;

        return $this;
    }

    /**
     * @return string
     */
    public function getMime()
    {
        return $this->mime;
    }

    /**
     * @param int $size
     *
     * @return self
     */
    public function setSize($size)
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Devuelve la ruta para los ficheros subidos.
     *
     * @return string
     */
    public function getUploadDirectory()
    {
        return __DIR__.'/../../../uploads';
    }

    /**
     * Guarda el fichero subido desde un formulario.
     *
     * @param mixed $upload
     *
     * @return self
     */
    public function setUpload($upload)
    {
        if ($upload instanceof UploadedFile) {
            $dir = $this->getUploadDirectory();
            $extension = $upload->getClientOriginalExtension();
            $name = sprintf('%s.%s', uniqid(), $extension);
            $path = sprintf('%s/%s', $dir, $name);

            $upload->move($dir, $name);
            $this
                ->setPath($path)
                ->setName($upload->getClientOriginalName())
                ->setMime($upload->getClientMimeType())
                ->setSize($upload->getClientSize())
            ;
        }

        return $this;
    }

    /**
     * Devuelve información para rellenar un formulario.
     *
     * @return UploadedFile
     */
    public function getUpload()
    {
        return new UploadedFile(
            $this->getPath(),
            $this->getName(),
            $this->getMime(),
            $this->getSize(),
            true
        );
    }

    /**
     * Devuelve el nombnre del  avatar subidos.
     *
     * @return string
     */
    public function getFileName()
    {
        $path = explode('/', $this->path);

        return end($path);
    }
}
