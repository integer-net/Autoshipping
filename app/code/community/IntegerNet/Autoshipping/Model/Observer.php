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
     * @param Varien_Event_Observer $observer
     * @event controller_action_postdispatch_checkout_cart_updatePost
     * @event controller_action_postdispatch_checkout_cart_add
     * @event controller_action_predispatch_checkout_cart_index
     * @event controller_action_postdispatch_sales_order_reorder
     * @event controller_action_postdispatch_checkout_cart_delete
     */
    public function addShipping($observer)
    {
        if (Mage::getStoreConfig('autoshipping/settings/enabled')) {
            if (!($country = $this->_getCoreSession()->getAutoShippingCountry())) {
                $country = Mage::getStoreConfig('autoshipping/settings/country_id');
                $this->_getCoreSession()->setAutoShippingCountry($country);
            }

            $quote = $this->_getCheckoutSession()->getQuote();
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

                    $this->_getCheckoutSession()->resetCheckout();

                } catch (Mage_Core_Exception $e) {
                    $this->_getCheckoutSession()->addError($e->getMessage());
                }
                catch (Exception $e) {
                    $this->_getCheckoutSession()->addException(
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
        if (Mage::getStoreConfig('autoshipping/settings/enabled')) {
            $country = Mage::getStoreConfig('autoshipping/settings/country_id');
            $sessionCountry = $this->_getCoreSession()->getAutoShippingCountry();

            if ($country != $sessionCountry) {
                $this->addShipping($observer);
            }
        }
    }

    /**
     * @return Mage_Checkout_Model_Session
     */
    protected function _getCheckoutSession()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * @return Mage_Core_Model_Session
     */
    protected function _getCoreSession()
    {
        return Mage::getSingleton('core/session');
    }
}
