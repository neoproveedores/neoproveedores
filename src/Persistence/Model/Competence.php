<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Competencia a valorar de un proveedor.
 *
 * @MongoDB\Document(
 *     collection="competences",
 *     repositoryClass="Persistence\Repository\CompetenceRepository"
 * )
 */
class Competence implements DocumentInterface
{
    use Properties\IdentityTrait;
    use Properties\NameTrait;

    /**
     * @param string $name
     */
    public function __construct($name = nulll)
    {
        $this->setName($name);
    }
}
