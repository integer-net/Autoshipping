IntegerNet_Autoshipping
=====================
Allows you to display shipping costs in cart and change the country for which the shipping cost is calculated.

Facts
-----
- version: 0.3.0
- extension key: IntegerNet_Autoshipping
- [extension on GitHub](https://github.com/integer-net/Autoshipping)
- [direct download link](https://github.com/integer-net/Autoshipping/archive/master.zip)

Description
-----------
This extension is a fork of [PRWD_Autoshipping](http://www.magentocommerce.com/magento-connect/prwd-auto-shipping.html).
It displays the shipping costs on the shopping cart page even if you haven't entered an address yet. It takes the
target country from the configuration.
If there is more than one allowed country, a dropdown is available on the shopping cart page which allow the
customer to change the target country.
You can now exclude shipping methods by configuration (useful for pickup for example)

Requirements
------------
- PHP >= 5.2.0
- Mage_Core
- Mage_Checkout

Compatibility
-------------
- Magento >= 1.4

Installation Instructions
-------------------------
1. Clone the module into your document root.
2. Clear the cache, logout from the admin panel and then login again.
3. Configure and activate the extension under System - Configuration - Sales - Auto Shipping.

Uninstallation
--------------
1. Remove all extension files from your Magento installation

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/integer-net/Autoshipping/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Andreas von Studnitz, [integer_net GmbH](http://www.integer-net.de)

Twitter: [@integer_net](https://twitter.com/integer_net)

Licence
-------
[GNU General Public License 3.0](http://www.gnu.org/licenses/)

Copyright
---------
(c) 2014 integer_net GmbH
