<?php
/**
 * Authorize.net AIM Payment Module V.1.0 created by Eric Stamper - 01/30/2004 Released under GPL
 *
  * @package languageDefines
 * @copyright Copyright 2003-2005 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: ESPAY.php 5422 2006-12-28 08:15:15Z drbyte $
 */


// Admin Configuration Items
  define('MODULE_PAYMENT_ESPAY_TEXT_CATALOG_TITLE', ' <img src="'.DIR_WS_CATALOG.DIR_WS_IMAGES.'sgo1.png" style="float:right;"/>');
  define('MODULE_PAYMENT_ESPAY_TEXT_ADMIN_TITLE', 'ESPay Payment Gateways'); // Payment option title as displayed in the admin
  define('MODULE_PAYMENT_ESPAY_TEXT_DESCRIPTION', (defined('MODULE_PAYMENT_IPAYMEX_TESTMODE') && MODULE_PAYMENT_IPAYMEX_TESTMODE == 'Production' ? '' : '<b>Accept payments for your products via ESPay Payment Gateways</b><br /><br />Version: 1.1 | By PT. Square Gate One | <a href="http://sgo.co.id">Visit Plugin Site</a>'));

// Catalog Items
  define('MODULE_PAYMENT_ESPAY_TEXT_JS_PAYMENTTYPE', '* Please Select Payment Method');
  define('MODULE_PAYMENT_ESPAY_TEXT_DECLINED_MESSAGE', 'Your credit card could not be authorized for this reason. Please correct any information and try again or contact us for further assistance.');
  define('MODULE_PAYMENT_ESPAY_TEXT_ERROR', 'Credit Card Error!');
?>