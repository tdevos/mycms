<?php

namespace Cms\MainBundle\Block;

use Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\AbstractBlock;
use Symfony\Cmf\Bundle\CoreBundle\Translatable\TranslatableInterface;

class AddressMapBlock extends AbstractBlock implements TranslatableInterface{

    public function getType(){
        return "cmf.block.simple";
    }

    public $companyName;
    public $streetAddress;
    public $addressLocality;
    public $addressRegion;
    public $postalCode;
    public $fullAddress;
    public $telephone;
    public $faxNumber;

    /**
     * @param mixed $addressLocality
     */
    public function setAddressLocality($addressLocality)
    {
        $this->addressLocality = $addressLocality;
    }

    /**
     * @return mixed
     */
    public function getAddressLocality()
    {
        return $this->addressLocality;
    }

    /**
     * @param mixed $addressRegion
     */
    public function setAddressRegion($addressRegion)
    {
        $this->addressRegion = $addressRegion;
    }

    /**
     * @return mixed
     */
    public function getAddressRegion()
    {
        return $this->addressRegion;
    }

    /**
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @param mixed $faxNumber
     */
    public function setFaxNumber($faxNumber)
    {
        $this->faxNumber = $faxNumber;
    }

    /**
     * @return mixed
     */
    public function getFaxNumber()
    {
        return $this->faxNumber;
    }

    /**
     * @param mixed $fullAddress
     */
    public function setFullAddress($fullAddress)
    {
        $this->fullAddress = $fullAddress;
    }

    /**
     * @return mixed
     */
    public function getFullAddress()
    {
        return $this->getStreetAddress() . " " . $this->getPostalCode() . " " . $this->getAddressLocality() . " " . $this->getAddressRegion();
    }

    /**
     * @param mixed $postalCode
     */
    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @return mixed
     */
    public function getPostalCode()
    {
        return $this->postalCode;
    }

    /**
     * @param mixed $streetAddress
     */
    public function setStreetAddress($streetAddress)
    {
        $this->streetAddress = $streetAddress;
    }

    /**
     * @return mixed
     */
    public function getStreetAddress()
    {
        return $this->streetAddress;
    }

    /**
     * @param mixed $telephone
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;
    }

    /**
     * @return mixed
     */
    public function getTelephone()
    {
        return $this->telephone;
    }


}