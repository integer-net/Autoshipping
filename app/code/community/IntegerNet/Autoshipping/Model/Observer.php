<?php
/**
 * integer_net Autoshipping Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Autoshipping
 * @copyright  Copyright (c) 2013 integer_net GmbH (http://www.integer-net.de/)
 * @license    http://opensource.org/licenses/gpl-3.0 GNU General Public License, version 3 (GPLv3)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 * @author     PRWD (http://www.prwd.co.uk)
 * */

class IntegerNet_Autoshipping_Model_Observer
{
    protected $_methodManuallyChanged;

    /**
     * Set configured country
     *
     * @param Varien_Event_Observer $observer
     * @event checkout_cart_save_before
     */
    public function prepareShippingAddress(Varien_Event_Observer $observer)
    {
        if (! Mage::getStoreConfigFlag('autoshipping/settings/enabled')) {
            return;
        }
        $quote = $this->_getCheckoutSession()->getQuote();
        if (! $quote->hasItems()) {
            return;
        }
        if (!($country = $this->_getCoreSession()->getAutoShippingCountry())) {
            $country = Mage::getStoreConfig('autoshipping/settings/country_id');
            $this->_getCoreSession()->setAutoShippingCountry($country);
        }

        $billingAddress = $quote->getBillingAddress();
        if (!$billingAddress->getCountryId()) {
            $billingAddress->setCountryId($country);
        }

        $shippingAddress = $quote->getShippingAddress();
        if (!$shippingAddress->getCountryId()) {
            $shippingAddress->setCountryId($country);
        }
        
        if (!$shippingAddress->getFreeMethodWeight()) {
            $shippingAddress->setFreeMethodWeight($shippingAddress->getWeight());
        }

        $this->_methodManuallyChanged = $this->_isMethodManuallyChanged($shippingAddress);
    }
    /**
     * Set shipping method
     * 
     * @param Varien_Event_Observer $observer
     * @event checkout_cart_save_after
     */
    public function addShipping(Varien_Event_Observer $observer)
    {
        if (! Mage::getStoreConfigFlag('autoshipping/settings/enabled')) {
            return;
        }
        $quote = $this->_getCheckoutSession()->getQuote();
        if (! $quote->hasItems()) {
            return;
        }

        $shippingAddress = $quote->getShippingAddress();
        $shippingAddress->setCollectShippingRates(true)->collectShippingRates();

        if($this->_methodManuallyChanged && $shippingAddress->getShippingMethod()) {
            // if the manually selected shipping method is still available, do nothing!
            return;
        }

        $rates = $shippingAddress->getGroupedAllShippingRates();

        if (count($rates)) {

            $topRates = reset($rates);
            foreach($topRates as $topRate) {

                /** @var Mage_Sales_Model_Quote_Address_Rate $topRate */

                if (in_array($topRate->getCarrier(), explode(',', Mage::getStoreConfig('autoshipping/settings/ignore_shipping_methods')))) {
                    continue;
                }

                try {
                    $shippingAddress->setShippingMethod($topRate->getCode());
                    $shippingDescription = $topRate->getCarrierTitle() . ' - ' . $topRate->getMethodTitle();
                    $shippingAddress->setShippingAmount($topRate->getPrice());
                    $shippingAddress->setBaseShippingAmount($topRate->getPrice());
                    $shippingAddress->setShippingDescription(trim($shippingDescription, ' -'));

                    $quote->save();

                    $this->_getCheckoutSession()->resetCheckout();

                    $this->_getCheckoutSession()->setAutoShippingMethod($topRate->getCode());

                } catch (Mage_Core_Exception $e) {
                    $this->_getCheckoutSession()->addError($e->getMessage());
                }
                catch (Exception $e) {
                    $this->_getCheckoutSession()->addException(
                        $e, Mage::helper('checkout')->__('Load customer quote error')
                    );
                }

                return;
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

        if (! Mage::getStoreConfigFlag('autoshipping/settings/show_country_selection_in_cart')) {
            return;
        }

        if ($block instanceof Mage_Tax_Block_Checkout_Shipping) {

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

    /**
     * @param $shippingAddress
     * @return bool
     */
    protected function _isMethodManuallyChanged($shippingAddress)
    {
        return $shippingAddress->getShippingMethod()
        && ($shippingAddress->getShippingMethod() != $this->_getCheckoutSession()->getAutoShippingMethod())
        && ($shippingAddress->getCountryId() == $this->_getCoreSession()->getAutoShippingCountry());
    }
}
