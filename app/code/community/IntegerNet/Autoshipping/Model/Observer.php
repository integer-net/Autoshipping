<?php

/**
 * Auto Shipping Module
 *
 * NOTICE OF LICENSE
 *
    Copyright (C) 2009 PRWD (http://www.prwd.co.uk)
    Copyright (C) 2013 integer_net GmbH (http://www.integer-net.de)

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
        if (Mage::getStoreConfigFlag('autoshipping/settings/enabled')) {
            if (!($country = $this->_getCoreSession()->getAutoShippingCountry())) {
                $country = Mage::getStoreConfig('autoshipping/settings/country_id');
                $this->_getCoreSession()->setAutoShippingCountry($country);
            }

            $quote = $this->_getCheckoutSession()->getQuote();
            $shippingAddress = $quote->getShippingAddress();

            $shippingAddress->setCountryId($country);
            $shippingAddress->setCollectShippingRates(true);

            $shippingAddress->collectShippingRates();

            $rates = $shippingAddress->getGroupedAllShippingRates();

            if (count($rates)) {
                $topRate = reset($rates);
                $code = $topRate[0]->code;

                try {
                    $shippingAddress->setShippingMethod($code);

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
        if (Mage::getStoreConfigFlag('autoshipping/settings/enabled')) {
            $country = Mage::getStoreConfig('autoshipping/settings/country_id');
            $sessionCountry = $this->_getCoreSession()->getAutoShippingCountry();

            if ($country != $sessionCountry) {
                $this->addShipping($observer);
            }
        }
    }

    /**
     * Show dropdown for country selection in cart before shipping cost
     *
     * @param Varien_Event_Observer $observer
     * @event core_block_abstract_to_html_before
     */
    public function beforeBlockToHtml($observer)
    {
        $block = $observer->getBlock();

        if ($block instanceof Mage_Tax_Block_Checkout_Shipping) {

            if (!Mage::getStoreConfigFlag('autoshipping/settings/show_country_selection_in_cart')) {
                return;
            }

            // show only on cart
            if (Mage::app()->getRequest()->getControllerName() != 'cart') {
                return;
            }

            // don't display if only 1 country allowed
            if (sizeof(explode(',', Mage::getStoreConfig('general/country/allow'))) <= 1) {
                return;
            }

            // replace total title
            $block->getTotal()->setTitle(
                $block->getLayout()->createBlock('autoshipping/country', 'checkout_cart_country_select')->toHtml()
            );
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
