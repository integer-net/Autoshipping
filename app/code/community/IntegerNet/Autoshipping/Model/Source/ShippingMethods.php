<?php
/**
 * integer_net Magento Module
 *
 * @category   IntegerNet
 * @package    IntegerNet_Autoshipping
 * @copyright  Copyright (c) 2014 integer_net GmbH (http://www.integer-net.de/)
 * @author     Andreas von Studnitz <avs@integer-net.de>
 */
class IntegerNet_Autoshipping_Model_Source_ShippingMethods
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(array(
            'value' => '',
            'label' => '',
        ));
        foreach(Mage::getStoreConfig('carriers') as $carrierCode => $carrierConfig) {
            $options[] = array(
                'value' => $carrierCode,
                'label' => Mage::getStoreConfig('carriers/' . $carrierCode . '/title') . ' [' . $carrierCode . ']',
            );
        }
        return $options;
    }
}