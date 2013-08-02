<?php
/**
 * integer_net Autoshipping Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Autoshipping
 * @copyright  Copyright (c) 2013 integer_net GmbH (http://www.integer-net.de/)
 * @license    http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_Autoshipping_Block_Country extends Mage_Directory_Block_Data
{
    public function __construct()
    {
        $this->setTemplate('checkout/cart/country.phtml');
    }

    /**
     * @return string
     */
    public function getSelectedCountryId()
    {
        return Mage::getSingleton('checkout/session')->getQuote()->getShippingAddress()->getCountryId();
    }
}