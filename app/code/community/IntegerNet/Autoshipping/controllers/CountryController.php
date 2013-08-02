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
class IntegerNet_Autoshipping_CountryController extends Mage_Core_Controller_Front_Action
{
    public function selectAction()
    {
        $countryId = $this->getRequest()->getParam('country_id');
        Mage::getSingleton('core/session')->setAutoShippingCountry($countryId);
        $this->_redirectReferer();
    }
}