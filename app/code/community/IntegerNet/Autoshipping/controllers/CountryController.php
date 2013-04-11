<?php
class IntegerNet_Autoshipping_CountryController extends Mage_Core_Controller_Front_Action
{
    public function selectAction()
    {
        $countryId = $this->getRequest()->getParam('country_id');
        Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->setCountryId($countryId)->save();
        $this->_redirectReferer();
    }
}