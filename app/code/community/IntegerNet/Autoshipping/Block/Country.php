<?php

class IntegerNet_Autoshipping_Block_Country extends Mage_Directory_Block_Data
{
    public function __construct()
    {
        $this->setTemplate('checkout/cart/country.phtml');
    }

    /**
     * @return string
     */
    public function getShippingCostPageUrl()
    {
        return Mage::getUrl(null, array('_direct' => 'versandkosten'));
    }

    public function getSelectedCountryId()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCountryId();
    }
}