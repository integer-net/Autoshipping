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