<?php

/**
 * PRWD Auto Shipping Module
 *
 * NOTICE OF LICENSE
 *
  Copyright (C) 2009 PRWD (http://www.prwd.co.uk)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class IntegerNet_Autoshipping_Model_Observer
{
	/**
	 * @param $observer Varien_Event_Observer $observer
     * @event controller_action_postdispatch_checkout_cart_updatePost
     * @event controller_action_postdispatch_checkout_cart_add
	*/
	public function addShipping($observer)
	{
		if (Mage::getStoreConfig('autoshipping/settings/enabled')) {

			$country = Mage::getStoreConfig('autoshipping/settings/country_id');
			Mage::getSingleton('core/session')->setAutoShippingCountry($country);

            /** @var $quote Mage_Sales_Model_Quote */
            $quote = Mage::getSingleton('checkout/session')->getQuote();
            $quote->getShippingAddress()
				->setCountryId($country)
				->setCollectShippingRates(true);
			
			$quote
				->getShippingAddress()->collectShippingRates();
				
			$rates = $quote
				->getShippingAddress()->getGroupedAllShippingRates();
			
			if (count($rates)) {
				$topRate = reset($rates);
				$code = $topRate[0]->code;
				
				try {
					$quote->getShippingAddress()
						->setShippingMethod($code);
						
					$quote->save();
					
					$this->_getSession()->resetCheckout();
				}
				catch (Mage_Core_Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
				}
				catch (Exception $e) {
                    $this->_getSession()->addException(
						$e, Mage::helper('checkout')->__('Load customer quote error')
					);
				}
			}
		}
	}

    /**
     * @param Varien_Event_Observer $observer
     * @event controller_action_predispatch
     */
    public function checkCountry($observer)
	{
		if (Mage::getStoreConfig('autoshipping/settings/enabled'))
		{
			$country = Mage::getStoreConfig('autoshipping/settings/country_id');
			$sessionCountry = Mage::getSingleton('core/session')->getAutoShippingCountry();
			
			if ($country != $sessionCountry) {
				$this->addShipping($observer);
			}
		}
	}

    /**
     * @return Mage_Checkout_Model_Session
     */
    public function _getSession()
    {
        return Mage::getSingleton('checkout/session');
    }
}
