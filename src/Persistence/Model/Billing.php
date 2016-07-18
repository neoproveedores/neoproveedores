<?php

namespace Persistence\Model;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * Información sobre facturación.
 *
 * @MongoDB\EmbeddedDocument
 */
class Billing
{
    use Properties\FilesTrait;
    use Properties\NotesTrait;

    /**
     * @MongoDB\String
     */
    protected $taxIdent;

    /**
     * @MongoDB\String
     */
    protected $bankAccount;

    /**
     * @MongoDB\String
     */
    protected $bankAccountCode;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\File")
     */
    protected $taxIdentFile;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\File")
     */
    protected $taxIdentAdditionalFile;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\File")
     */
    protected $taxCertificateFile;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\File")
     */
    protected $socialSecurityCertificateFile;

    /**
     * @MongoDB\EmbedOne(targetDocument="Persistence\Model\File")
     */
    protected $socialSecurityLastReceiptFile;

    /**
     * Clona los documentos incrustados
     */
    public function __clone()
    {
        $embeds = [
            'taxIdentFile',
            'taxIdentAdditionalFile',
            'taxCertificateFile',
            'socialSecurityCertificateFile',
        ];

        foreach ($embeds as $embed) {
            if (is_object($this->$embed)) {
                $this->$embed = clone $this->$embed;
            }
        }
    }

    /**
     * @param string $taxIdent
     *
     * @return self
     */
    public function setTaxIdent($taxIdent)
    {
        $this->taxIdent = $taxIdent;

        return $this;
    }

    /**
     * @return string
     */
    public function getTaxIdent()
    {
        return $this->taxIdent;
    }

    /**
     * @param string $bankAccount
     *
     * @return self
     */
    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    /**
     * @param string $bankAccountCode
     *
     * @return self
     */
    public function setBankAccountCode($bankAccountCode)
    {
        $this->bankAccountCode = $bankAccountCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getBankAccountCode()
    {
        return $this->bankAccountCode;
    }

    /**
     * @param File $file
     *
     * @return self
     */
    public function setTaxIdentFile($file)
    {
        $this->taxIdentFile = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getTaxIdentFile()
    {
        return $this->taxIdentFile;
    }

    /**
     * @param File $file
     *
     * @return self
     */
    public function setTaxIdentAdditionalFile($file)
    {
        $this->taxIdentAdditionalFile = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getTaxIdentAdditionalFile()
    {
        return $this->taxIdentAdditionalFile;
    }

    /**
     * @param File $file
     *
     * @return self
     */
    public function setTaxCertificateFile($file)
    {
        $this->taxCertificateFile = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getTaxCertificateFile()
    {
        return $this->taxCertificateFile;
    }

    /**
     * @param File $file
     *
     * @return self
     */
    public function setSocialSecurityCertificateFile($file)
    {
        $this->socialSecurityCertificateFile = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getSocialSecurityCertificateFile()
    {
        return $this->socialSecurityCertificateFile;
    }

    /**
     * @param File $file
     *
     * @return self
     */
    public function setSocialSecurityLastReceiptFile($file)
    {
        $this->socialSecurityLastReceiptFile = $file;

        return $this;
    }

    /**
     * @return File
     */
    public function getSocialSecurityLastReceiptFile()
    {
        return $this->socialSecurityLastReceiptFile;
    }
}
