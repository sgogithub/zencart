<?php
/**
 * ESPay payment method class
 *
 * @package paymentMethod
 * @copyright Copyright 2003-2006 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: espay.php 5435 2006-12-28 19:09:50Z drbyte $
 */
/**
 * ESPay Payment Module (AIM version)
 * You must have SSL active on your server to be compliant with merchant TOS
 *
 */
class espay extends base {
  var $code, $title, $description, $enabled, $response ;

  function espay() {
    global $order;
    $this->code = 'espay';
    
    $this->cc_map = array(
	    'AMEX' => 'American Express',
		'DINS' => 'Diners Card',
		'ELV' => 'ELV',
		'JCB' => 'JCB Card',
		'MSCD' => 'MasterCard',
		'DMC' => 'MasterCard Debit',
		'MASTERPASS' => 'MasterPass',
		'MAES' => 'Maestro',
		'VISA' => 'Visa',
		'VISD' => 'Visa Debit',
		'VIED' => 'Visa Electron',
		'VISP' => 'Visa Purchasing',
		'VME' => 'V.me',
		'0' => 'I will choose a method later'
		) ;
    
    
    if (IS_ADMIN_FLAG === true) {
      // Payment module title in Admin
//      $this->title = MODULE_PAYMENT_ESPAY_TEXT_ADMIN_TITLE;
      if (MODULE_PAYMENT_ESPAY_STATUS == 'True' && (MODULE_PAYMENT_ESPAY_MERCHID == 'testing' || MODULE_PAYMENT_ESPAY_PASS == 'Test')) {
//        $this->title .=  '<span class="alert"> (Not Configured)</span>';
        $this->title .=  MODULE_PAYMENT_ESPAY_TEXT_CATALOG_TITLE.' '.MODULE_PAYMENT_ESPAY_TEXT_ADMIN_TITLE;
        
      }
      else{
      	$this->title = MODULE_PAYMENT_ESPAY_TEXT_CATALOG_TITLE.' '.MODULE_PAYMENT_ESPAY_TEXT_ADMIN_TITLE;
      }
    } else {
      $this->title = MODULE_PAYMENT_ESPAY_TEXT_CATALOG_TITLE.' '.MODULE_PAYMENT_ESPAY_TEXT_ADMIN_TITLE; // Payment module title in Catalog
    }
    
    $this->description = MODULE_PAYMENT_ESPAY_TEXT_DESCRIPTION; // Descriptive Info about module in Admin
    $this->enabled = ((MODULE_PAYMENT_ESPAY_STATUS == 'True') ? true : false); // Whether the module is installed or not
    $this->sort_order = MODULE_PAYMENT_ESPAY_SORT_ORDER; // Sort Order of this payment option on the customer payment page
//    $this->form_action_url = zen_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false); // Page to go to upon submitting page info
    $this->form_action_url = zen_href_link(checkout_espay, '', 'SSL', false); // Page to go to upon submitting page info
    
//    $this->form_action_url = 'https://www.stratapay.com.au/paypage.aspx';
    
    if ((int)MODULE_PAYMENT_ESPAY_ORDER_STATUS_ID > 0) {
      $this->order_status = MODULE_PAYMENT_ESPAY_ORDER_STATUS_ID;
    }

    if (is_object($order)) $this->update_status();
  }
  /**
   * calculate zone matches and flag settings to determine whether this module should display to customers or not
   *
   */
  function update_status() {
    global $order, $db;

    if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_ESPAY_ZONE > 0) ) {
      $check_flag = false;
      $check = $db->Execute("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_ESPAY_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
      while (!$check->EOF) {
        if ($check->fields['zone_id'] < 1) {
          $check_flag = true;
          break;
        } elseif ($check->fields['zone_id'] == $order->billing['zone_id']) {
          $check_flag = true;
          break;
        }
        $check->MoveNext();
      }

      if ($check_flag == false) {
        $this->enabled = false;
      }
    }
  }
  /**
   * JS validation which does error-checking of data-entry if this module is selected for use
   * (Number, Owner, and CVV Lengths)
   *
   * @return string
   */

      function javascript_validation() {
//        return false;
//			die('test');
		    $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
		    '    var paymentType = document.checkout_payment.paymentType.value;' . "\n";
		    
		    $js .= '    if (paymentType == "") {' . "\n" .
		    '      error_message = error_message + "' . MODULE_PAYMENT_ESPAY_TEXT_JS_PAYMENTTYPE . '";' . "\n" .
		    '      error = 1;' . "\n" .
		    '    }' . "\n" .
		    
		
		    $js .= '  }' . "\n";
		
		    return $js;
      }
  /**
   * Display Credit Card Information Submission Fields on the Checkout Payment Page
   *
   * @return array
   */
  
	  
  function _draw_radio_menu($select_array, $chkd='') {
		$string = "\n".'<ul id="espay-payment-radio">'."\n";

//		foreach ($select_array as $key=>$val) {
//			$img = '<img src="https://secure.sgo.co.id/images/products/'.$val['productCode'].'.png" width="100px"> Payment Using '.$val['productName'].'';
//			$string .= '<li id="' . strtolower( trim($key) ) . '-li"><span id="' . strtolower( trim($key) ) . '-span-begin"></span>
//			<input type="radio" name="paymentType" id="paymentType" value="'.$val['productName'].','.$val['bankCode'].','.$val['productCode'].'"
//			';
//			
// 			if ($chkd == $key) $string .= ' CHECKED';
//
//			$string .= ' id="' . strtolower($key . '-paymentType"').  ' onclick="document.getElementById(\'pmt-espay\').checked=\'true\';"><label id="'.strtolower($key . '-label').'" for="'.strtolower($key . '-paymentType').'" class="radioButtonLabel" onclick="document.getElementById(\'pmt-espay\').checked=\'true\';">' . $img . '</label><span id="' . strtolower( trim($key) ) . '-span-end"></span></li>'."\n";
//		}

  		foreach ($select_array as $key=>$val) {
  			$string .= '<li id="' . strtolower( trim($key) ) . '-li">';
			$string .='
	   			<input type="radio" name="paymentType" id="paymentType" value="'.$val['productName'].','.$val['bankCode'].','.$val['productCode'].'"> 
		   		<img align="middle" src="https://secure.sgo.co.id/images/products/'.$val['productCode'].'.png" width="90" height="80" style="border-radius:30px;background:#7dd4e1;padding:15px;border:4px solid #fff;"/>
				Payment Using '.$val['productName'].'
		   ';
			
			$string .= '<li>';
  		}
  		
  		$string .= "<div align=center>";
	    $string .='Powered by <a href="http://www.espay.id/"> <b><font color="#7dd4e1">espay.id</font></b></a>';
	    $string .= "</div>";

		return $string;
  }
		
  function selection() {
  		return array('id' => $this->code,
					 'module' => MODULE_PAYMENT_ESPAY_TEXT_CATALOG_TITLE.' '.MODULE_PAYMENT_ESPAY_TEXT_ADMIN_TITLE.$this->_draw_radio_menu($this->_cc_map(), $_SESSION['paymentType'] ));
  }
  
   function _cc_map(){
//		$arr = explode(',', "AMEX, DINS, ELV, JCB, MSCD,VISP");
//		$out = array(''=>'I\'ll select a card later');
//		
//		foreach($arr as $val){
//			$out[trim($val)] = $this->cc_map[trim($val)];
//			echo'<pre>';
//			var_dump($val);
//			echo'</pre>';
//		}
   	  	/////////////////////////////////////////////////////////////
   	  	
 			$callApiProduct = $this->callApiProduct();
           	foreach ($callApiProduct as $value) {
        		$valJson = json_encode($value);
        		$valJsonPost = json_decode($valJson);
				$out[] = array(
								'bankCode'=>trim($valJsonPost->bankCode),
								'productCode'=>trim($valJsonPost->productCode),
								'productName'=>trim($valJsonPost->productName)
							);
        		
//				$out[trim($valJsonPost->productCode)] = trim($valJsonPost->productName);
           		
           	}
		return $out;
   }
   
 		private function callApiProduct(){
 			$apikey = MODULE_PAYMENT_ESPAY_MERCHID;
 			
	    	$url =  MODULE_PAYMENT_ESPAY_MODE == 'PRODUCTION'? 'https://api.espay.id/rest/merchant/merchantinfo' : 'https://sandbox-api.espay.id/rest/merchant/merchantinfo';
//	        $url = 'http://116.90.162.170:10809/rest/merchant/merchantinfo';  
//	        $key =   Mage::getStoreConfig('payment/espay/paymentid');
	        $key = $apikey;//'7ea1d02c9fab152d9c82c9415870b876';  
	        $request = 'key='.$key;
	
	        $url =
	        $curl = curl_init($url);
	        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($curl, CURLOPT_POST, true);
	        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
	
	        curl_setopt($curl, CURLOPT_HEADER, false);
	        // curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
	        curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1); // use http 1.1
	        curl_setopt($curl, CURLOPT_TIMEOUT, 60);
	        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 60);
	        // curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
	
	        // NOTE: skip SSL certificate verification (this allows sending request to hosts with self signed certificates, but reduces security)
	        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
	        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	
	        // enable ssl version 3
	        // this is added because mandiri ecash case that ssl version that have been not supported before
	        curl_setopt($curl, CURLOPT_SSLVERSION, 1);
	
	        curl_setopt($curl, CURLOPT_VERBOSE, true);
	        // save to temporary file (php built in stream), cannot save to php://memory
	        $verbose = fopen('php://temp', 'rw+');
	        curl_setopt($curl, CURLOPT_STDERR, $verbose);
	
	        $response = curl_exec($curl);
	
	        $response = json_decode($response);
	        
	        return $response->data;
	  }
  /**
   * Evaluates the Credit Card Type for acceptance and the validity of the Credit Card Number & Expiration Date
   *
   */
//  function pre_confirmation_check() {
//    global $_POST, $messageStack;
//
//    include(DIR_WS_CLASSES . 'cc_validation.php');
//
//    $cc_validation = new cc_validation();
//    $result = $cc_validation->validate($_POST['paymentType'], $_POST['ipaymex_cc_expires_month'], $_POST['ipaymex_cc_expires_year'], $_POST['ipaymex_cc_cvv']);
//    $error = '';
//    switch ($result) {
//      case -1:
//      $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->cc_number, 0, 4));
//      break;
//      case -2:
//      case -3:
//      case -4:
//      $error = TEXT_CCVAL_ERROR_INVALID_DATE;
//      break;
//      case false:
//      $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
//      break;
//    }
//
//    if ( ($result == false) || ($result < 1) ) {
//      $payment_error_return = 'payment_error=' . $this->code . '&ipaymex_cc_owner=' . urlencode($_POST['ipaymex_cc_owner']) . '&ipaymex_cc_expires_month=' . $_POST['ipaymex_cc_expires_month'] . '&ipaymex_cc_expires_year=' . $_POST['ipaymex_cc_expires_year'];
//      $messageStack->add_session('checkout_payment', $error . '<!-- ['.$this->code.'] -->', 'error');
//      zen_redirect(zen_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
//    }
//
//    $this->cc_card_type = $cc_validation->cc_type;
//    $this->cc_card_number = $cc_validation->cc_number;
//    $this->cc_expiry_month = $cc_validation->cc_expiry_month;
//    $this->cc_expiry_year = $cc_validation->cc_expiry_year;
//  }

 	function pre_confirmation_check() {
 		global $order;
 		
		if(isset($_POST) && isset($_POST['paymentType']) ){
			$_SESSION['paymentType'] = $_POST['paymentType'];
			$espayproduct = $_SESSION['paymentType']."";

			$classes=explode(",",$espayproduct); 
			$productName = $classes[0];
			$bankCode = $classes[1];
			$productCode = $classes[2];
        	
			$_POST['paymentType'] = 'Online Payment: '.$productName;
			$_POST['bankCode'] = $bankCode;
			$_POST['productName'] = $productName;
			$_POST['productCode'] = $productCode;
			
//			var_dump($order);
			
			$_POST['ordAmt'] = $order->info['total'];
			$_POST['ccy'] = $order->info['currency'];
			$_POST['min_order_total'] = MODULE_PAYMENT_ESPAY_MIN_ORDER;
			$_POST['max_order_total'] = MODULE_PAYMENT_ESPAY_MAX_ORDER;
		}else{
			unset($_SESSION['paymentType']);
		}
        return false;
    }
  /**
   * Display Credit Card Information on the Checkout Confirmation Page
   *
   * @return array
   */
	
  function confirmation() {
    global $_POST;
    
//    $url = (isset($_SERVER['HTTPS']) ? 'https://':'http://').$_SERVER['HTTP_HOST'].'/';
//    $url_folder = $_SERVER['REQUEST_URI'];
    if($_POST['paymentType'] == ''){
    	?>
    	 <script>
            setTimeout(function(){
                window.location.href="./"; // The URL that will be redirected too.
            }, 300); // The bigger the number the longer the delay.
            alert("Payment method is empty");
        </script>
    	<?php 
    }
    
  	if($_POST['ordAmt'] < $_POST['min_order_total']){
				echo 'Error: Amount cannot be lower than '.$_POST['ccy'].number_format($_POST['min_order_total'], 0).'';
				?>
			<script>
	            setTimeout(function(){
	                window.location.href="./"; // The URL that will be redirected too.
	            }, 300); // The bigger the number the longer the delay.
	            alert("Error: Amount cannot be lower than <?=$_POST['ccy'].number_format($_POST['min_order_total'], 0)?>");
	        </script>
		<?php 
	}
	elseif($_POST['ordAmt'] > $_POST['max_order_total']){
				echo 'Error: Total amount is exceeding your maximum amount '.$_POST['ccy'].number_format($_POST['max_order_total'], 2).'';
				?>
			<script>
	            setTimeout(function(){
	                window.location.href="./"; // The URL that will be redirected too.
	            }, 300); // The bigger the number the longer the delay.
	            alert("Error: Total amount is exceeding your maximum amount <?=$_POST['ccy'].number_format($_POST['max_order_total'], 0)?>");
	        </script>
		<?php 
	}
    
    $productCode = $_POST['productCode'];
    $productName = $_POST['productName'];
    $bankCode = $_POST['bankCode'];
    
    $confirmation = array(//'title' => MODULE_PAYMENT_ESPAY_TEXT_CATALOG_TITLE, // Redundant
                          'fields' => array(
                                            array(
//                                            	  'title' => MODULE_PAYMENT_IPAYMEX_TEXT_CVV,
                                                  'field' => $_POST['paymentType'])));
    return $confirmation;
  }
  /**
   * Build the data and actions to process when the "Submit" button is pressed on the order-confirmation screen.
   * This sends the data to the payment gateway for processing.
   * (These are hidden fields on the checkout confirmation page)
   *
   * @return string
   */
  function process_button() {
  	  global $_POST, $languages_id, $shipping_cost, $total_cost, $shipping_selected, $shipping_method, $currencies, $currency, $customer_id , $db, $order;
	  $cartId = zen_session_id();
      $currency = $_SESSION['currency'];
      $OrderAmt = number_format($order->info['total'] * $currencies->get_value($currency), $currencies->get_decimal_places($currency), '.', '') ; 
      $symbol_left = $currencies->currencies[$currency]['symbol_left'];
      $symbol_right = $currencies->currencies[$currency]['symbol_right'];
      $productCode = $_POST['productCode'];
      
  			if($productCode == 'CREDITCARD'){
    	    	
    	    	if($productCode == 'BCAKLIKPAY')
        		{
//		        	$fee = ($this->fee_bca_klikpay == '')?0:$this->fee_bca_klikpay;
		        	$fee = MODULE_PAYMENT_ESPAY_FEE_BCA;
        		}
        		elseif($productCode == 'XLTUNAI')
        		{
//		        	$fee = ($this->fee_xl_tunai == '')?0:$this->fee_xl_tunai;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_XL_TUNAI;
        		}
        		elseif($productCode == 'BIIATM')
        		{
//		        	$fee = ($this->fee_bii_atm == '')?0:$this->fee_bii_atm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_BII_ATM;
        		}
        		elseif($productCode == 'BNIDBO')
        		{
//		        	$fee = ($this->fee_bnidbo == '')?0:$this->fee_bnidbo;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_BNI_DEBIT_ONLINE;
        		}
        		elseif($productCode == 'DANAMONOB')
        		{
//		        	$fee = ($this->fee_danamon_ob == '')?0:$this->fee_danamon_ob;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_DANAMON_OB;
        		}
        		elseif($productCode == 'DKIIB')
        		{
//		        	$fee = ($this->fee_dki_ib == '')?0:$this->fee_dki_ib;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_DKI_IB;
        		}
        		elseif($productCode == 'MANDIRIIB')
        		{
//		        	$fee = ($this->fee_mandiri_ib == '')?0:$this->fee_mandiri_ib;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MANDIRI_IB;
        		}
        		elseif($productCode == 'MANDIRIECASH')
        		{
//		        	$fee = ($this->fee_mandiri_ecash == '')?0:$this->fee_mandiri_ecash;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MANDIRI_ECH;
        		}
        		elseif($productCode == 'FINPAY195')
        		{
//		        	$fee = ($this->fee_finpay == '')?0:$this->fee_finpay;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_FINPAY;
        		}
        		elseif($productCode == 'CREDITCARD')
        		{
//		        	$fee = ($this->fee_credit_card == '')?0:$this->fee_credit_card;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_CREDIT_CARD;
        		}
        		elseif($productCode == 'MANDIRISMS')
        		{
//		        	$fee = ($this->fee_mandiri_sms == '')?0:$this->fee_mandiri_sms;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MANDIRI_SM;
        		}
        		elseif($productCode == 'MAYAPADAIB')
        		{
//		        	$fee = ($this->fee_mayapada_ib == '')?0:$this->fee_mayapada_ib;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MAYAPADA_IB;
        		}
        		elseif($productCode == 'MUAMALATATM')
        		{
//		        	$fee = ($this->fee_mualamatatm == '')?0:$this->fee_mualamatatm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MUAMALAT_ATM;
        		}
        		elseif($productCode == 'NOBUPAY')
        		{
//		        	$fee = ($this->fee_nobupay == '')?0:$this->fee_nobupay;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_NOBUPAY;
        		}
        		elseif($productCode == 'PERMATAATM')
        		{
//		        	$fee = ($this->fee_permata_atm == '')?0:$this->fee_permata_atm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_PERMATA_ATM;
        		}    
        		elseif($productCode == 'PERMATANETPAY')
        		{
//		        	$fee = ($this->fee_permata_atm == '')?0:$this->fee_permata_atm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_PERMATA_NET_PAY;
        		}    
				else
        		{
		        	$fee = 0;
        		}
        		
        		
        		$creditcardfee = (MODULE_PAYMENT_ESPAY_CREDIT_CARD_FEE_PERSEN == '')?0:MODULE_PAYMENT_ESPAY_CREDIT_CARD_FEE_PERSEN;
    	    	$amount = $OrderAmt;
		        
	            $amountcredit = $amount + $fee;
	            $amountFinish =  (($amountcredit * $creditcardfee)/100) + $fee;
		        $totalAmount = $amount + $amountFinish; //disc rate
		        
		        $perhitunganCreditcardfee = ($amountcredit * $creditcardfee)/100; //dari % convert to RP
        	}
        	else{
        		if($productCode == 'BCAKLIKPAY')
        		{
//		        	$fee = ($this->fee_bca_klikpay == '')?0:$this->fee_bca_klikpay;
		        	$fee = MODULE_PAYMENT_ESPAY_FEE_BCA;
        		}
        		elseif($productCode == 'XLTUNAI')
        		{
//		        	$fee = ($this->fee_xl_tunai == '')?0:$this->fee_xl_tunai;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_XL_TUNAI;
        		}
        		elseif($productCode == 'BIIATM')
        		{
//		        	$fee = ($this->fee_bii_atm == '')?0:$this->fee_bii_atm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_BII_ATM;
        		}
        		elseif($productCode == 'BNIDBO')
        		{
//		        	$fee = ($this->fee_bnidbo == '')?0:$this->fee_bnidbo;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_BNI_DEBIT_ONLINE;
        		}
        		elseif($productCode == 'DANAMONOB')
        		{
//		        	$fee = ($this->fee_danamon_ob == '')?0:$this->fee_danamon_ob;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_DANAMON_OB;
        		}
        		elseif($productCode == 'DKIIB')
        		{
//		        	$fee = ($this->fee_dki_ib == '')?0:$this->fee_dki_ib;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_DKI_IB;
        		}
        		elseif($productCode == 'MANDIRIIB')
        		{
//		        	$fee = ($this->fee_mandiri_ib == '')?0:$this->fee_mandiri_ib;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MANDIRI_IB;
        		}
        		elseif($productCode == 'MANDIRIECASH')
        		{
//		        	$fee = ($this->fee_mandiri_ecash == '')?0:$this->fee_mandiri_ecash;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MANDIRI_ECH;
        		}
        		elseif($productCode == 'FINPAY195')
        		{
//		        	$fee = ($this->fee_finpay == '')?0:$this->fee_finpay;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_FINPAY;
        		}
        		elseif($productCode == 'CREDITCARD')
        		{
//		        	$fee = ($this->fee_credit_card == '')?0:$this->fee_credit_card;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_CREDIT_CARD;
        		}
        		elseif($productCode == 'MANDIRISMS')
        		{
//		        	$fee = ($this->fee_mandiri_sms == '')?0:$this->fee_mandiri_sms;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MANDIRI_SM;
        		}
        		elseif($productCode == 'MAYAPADAIB')
        		{
//		        	$fee = ($this->fee_mayapada_ib == '')?0:$this->fee_mayapada_ib;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MAYAPADA_IB;
        		}
        		elseif($productCode == 'MUAMALATATM')
        		{
//		        	$fee = ($this->fee_mualamatatm == '')?0:$this->fee_mualamatatm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_MUAMALAT_ATM;
        		}
        		elseif($productCode == 'NOBUPAY')
        		{
//		        	$fee = ($this->fee_nobupay == '')?0:$this->fee_nobupay;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_NOBUPAY;
        		}
        		elseif($productCode == 'PERMATAATM')
        		{
//		        	$fee = ($this->fee_permata_atm == '')?0:$this->fee_permata_atm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_PERMATA_ATM;
        		}  
        				elseif($productCode == 'PERMATANETPAY')
        		{
//		        	$fee = ($this->fee_permata_atm == '')?0:$this->fee_permata_atm;
        			$fee = MODULE_PAYMENT_ESPAY_FEE_PERMATA_NET_PAY;
        		} 
        		else
        		{
		        	$fee = 0;
        		}
        		
	        	$creditcardfee = 0;
	        	$totalAmount = $OrderAmt + $fee;
        	}
        	
  	  $Amt = number_format($fee, 0).$symbol_right; 
//  	  var_dump($currencies);
	  $_SESSION['txtamt'] = $symbol_left.$Amt;
  	  $_SESSION['valueamt'] = $fee;
  	?>
		<div id="orderTotals">
		<div id="otshipping">
		    <div class="totalBox larger forward"><?php echo $symbol_left?><?php echo $Amt?></div>
		    <div class="lineTitle larger forward">Transaction Fee:</div>
		</div>
		<br class="clearBoth" />
		<?php 
		if($productCode == 'CREDITCARD'){
		?>
			<div id="otshipping">
			    <div class="totalBox larger forward"><?php 
			    echo $symbol_left.number_format($perhitunganCreditcardfee, 0).$symbol_right;
			    $_SESSION['txtcreditcardfee'] = $symbol_left.number_format($perhitunganCreditcardfee, 0).$symbol_right;
			    $_SESSION['valuecreditcardfee'] = $perhitunganCreditcardfee;
			    $_SESSION['creditcardfee'] = $creditcardfee;
			    ?></div>
			    <div class="lineTitle larger forward">Merchant Discount Rate:</div>
			</div>
			<br class="clearBoth" />
		<?php }?>
		<div id="ottotal">
		    <div class="totalBox larger forward"><?php 
		    	echo $symbol_left;?><?php echo number_format($totalAmount, 0).$symbol_right;
		    	$_SESSION['txttotalamount'] = $symbol_left.number_format($totalAmount, 0).$symbol_right;
		    	$_SESSION['valuetotalamount'] = $totalAmount;
		    ?>
		    </div>
		    <div class="lineTitle larger forward">Total Amount:</div>
		</div>
		<br class="clearBoth" />
		</div>
  	<?php
  }
  /**
   * Store the CC info to the order and process any results that come back from the payment gateway
   *
   */

	function before_process() {
		global $_POST, $response, $db, $order, $messageStack;
	    
		$espayproduct = $_SESSION['paymentType'];
		$classes=explode(",",$espayproduct); 
		$productName = $classes[0];
		$bankCode = $classes[1];
		$productCode = $classes[2];
        	
		$_POST['paymentType'] = 'Online Payment: '.$productName;
		$_POST['bankCode'] = $bankCode;
		$_POST['productName'] = $productName;
		$_POST['productCode'] = $productCode;
		
	    // DATA PREPARATION SECTION
	    unset($submit_data);  // Cleans out any previous data stored in the variable
	
	    // Create a string that contains a listing of products ordered for the description field
	    $description = '';
	    for ($i=0; $i<sizeof($order->products); $i++) {
	      $description .= $order->products[$i]['name'] . '(qty: ' . $order->products[$i]['qty'] . ') + ';
	    }
	    // Remove the last "\n" from the string
	    $description = substr($description, 0, -2);
	
	    // Create a variable that holds the order time
	    $order_time = date("F j, Y, g:i a");
	
	    // Calculate the next expected order id
	    $last_order_id = $db->Execute("select * from " . TABLE_ORDERS . " order by orders_id desc limit 1");
	    $new_order_id = $last_order_id->fields['orders_id'];
	    
	    $new_order_id = ($new_order_id + 1);
		
		return false;	
	}


 
  /**
   * Post-process activities.
   *
   * @return boolean
   */
  function after_process() {
    	global $insert_id, $db, $_POST, $order;
    
    	$espayproduct = $_SESSION['paymentType'];
		$classes=explode(",",$espayproduct); 
		$productName = $classes[0];
		$bankCode = $classes[1];
		$productCode = $classes[2];
        	
		$_POST['paymentType'] = 'Online Payment: '.$productName;
		$_POST['bankCode'] = $bankCode;
		$_POST['productName'] = $productName;
		$_POST['productCode'] = $productCode;
		
	    // DATA PREPARATION SECTION
	    unset($submit_data);  // Cleans out any previous data stored in the variable
	
	    // Create a string that contains a listing of products ordered for the description field
	    $description = '';
	    for ($i=0; $i<sizeof($order->products); $i++) {
	      $description .= $order->products[$i]['name'] . '(qty: ' . $order->products[$i]['qty'] . ') + ';
	    }
	    // Remove the last "\n" from the string
	    $description = substr($description, 0, -2);
	
	    // Create a variable that holds the order time
	    $order_time = date("F j, Y, g:i a");
	
	    // Calculate the next expected order id
	    $last_order_id = $db->Execute("select * from " . TABLE_ORDERS . " order by orders_id desc limit 1");
	    $new_order_id = $last_order_id->fields['orders_id'];
	    
	    $new_order_id = ($new_order_id + 1);
		
	    $db->Execute("UPDATE `".TABLE_ORDERS."` SET payment_method = '".MODULE_PAYMENT_ESPAY_TEXT_CATALOG_TITLE.' '.$_POST['productName']. ' via '.MODULE_PAYMENT_ESPAY_TEXT_ADMIN_TITLE."' WHERE orders_id = '".$new_order_id."'");
    
	    
	    $last_order_status = $db->Execute("select * from " . TABLE_ORDERS_STATUS . " order by orders_status_id desc limit 1");
	    $new_order_status = $last_order_status->fields['orders_status_id'];
	    
	    $new_order_status = ($new_order_status + 1);
	    	
	    $search_order_status = $db->Execute("select * from " . TABLE_ORDERS_STATUS . " WHERE orders_status_name= 'Waiting Payment'");
	    $search_order_status = $search_order_status->fields['orders_status_id'];
	    $or = $search_order_status;
	    if(count($search_order_status) < 1){
	    	$db->Execute("insert into " . TABLE_ORDERS_STATUS . " (orders_status_id, language_id, orders_status_name) values ('" . $new_order_status . ".' , '1','Waiting Payment')");
	    	$orders_status_id = $new_order_status;
	    }
	    else{
	    	$orders_status_id = $or;
	    }
	    
	    if($productCode == 'CREDITCARD'){
	    	$_SESSION['txtcreditcardfee'];
			$_SESSION['valuecreditcardfee'];
	    	
	    	$title1 = "Transaction Fee:";
	    	$title2 = "Merchant Discount Rate:";
	    	$class1 = "ot_transactionfee";
	    	$class2 = "ot_creditcardfee";
	    	
	    	$db->Execute("insert into " . TABLE_ORDERS_TOTAL . " (orders_id, title, text, value, class, sort_order) values ('" . $new_order_id . ".' , '". $title1 . "','" . $_SESSION['txtamt'] . "','" . $_SESSION['valueamt'] . "','" . $class1 . "','500')");
    		$db->Execute("insert into " . TABLE_ORDERS_TOTAL . " (orders_id, title, text, value, class, sort_order) values ('" . $new_order_id . ".' , '". $title2 . "','" . $_SESSION['txtcreditcardfee'] . "','" . $_SESSION['valuecreditcardfee'] . "','" . $class2 . "','700')");
	    	$db->Execute("UPDATE `".TABLE_ORDERS_TOTAL."` SET text = '".$_SESSION['txttotalamount']."',value = '".$_SESSION['valuetotalamount']."'  WHERE orders_id = '".$new_order_id."' and class = 'ot_total'");
	    }
	    else{
	    	$title1 = "Transaction Fee:";
	    	$class1 = "ot_transactionfee";
	    	
	    	$db->Execute("insert into " . TABLE_ORDERS_TOTAL . " (orders_id, title, text, value, class, sort_order) values ('" . $new_order_id . ".' , '". $title1 . "','" . $_SESSION['txtamt'] . "','" . $_SESSION['valueamt'] . "','" . $class1 . "','500')");
    		$db->Execute("UPDATE `".TABLE_ORDERS_TOTAL."` SET text = '".$_SESSION['txttotalamount']."',value = '".$_SESSION['valuetotalamount']."'  WHERE orders_id = '".$new_order_id."' and class = 'ot_total'");
	    }
	    
	    	$db->Execute("UPDATE `".TABLE_ORDERS_STATUS_HISTORY."` SET orders_status_id = '".$orders_status_id."' WHERE orders_id = '".$new_order_id."'");
	    	$db->Execute("UPDATE `".TABLE_ORDERS."` SET orders_status = '".$orders_status_id."' WHERE orders_id = '".$new_order_id."'");
	    
//	    	$db->Execute("insert into " . TABLE_ORDERS_ESPAY_HISTORY . " (orders_id) values ('" . $new_order_id . ".')");
//	    	$db->Execute("insert into " . TABLE_ORDERS_ESPAY_HISTORY . " (payment_method, orders_id, orders_status_id, date_added) values ('" . $new_order_id . "' , '". (int)$insert_id . "','" . $this->order_status . "', now() )");
    
	    	$db->Execute("insert into orders_espay_history (payment_method, orders_id, orders_status_id, date_added, creditcard_fee) values ('" . $espayproduct . "' , '". (int)$insert_id . "','" . $orders_status_id . "', now(),'" . $_SESSION['creditcardfee'] . "' )");
	    	
    return false;
  }
  /**
   * Used to display error message details
   *
   * @return array
   */
  function get_error() {
    global $_GET;

    $error = array('title' => MODULE_PAYMENT_ESPAY_TEXT_ERROR,
                   'error' => stripslashes(urldecode($_GET['error'])));

    return $error;
  }
  /**
   * Check to see whether module is installed
   *
   * @return boolean
   */
  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = $db->Execute("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_ESPAY_STATUS'");
      $this->_check = $check_query->RecordCount();
    }
    return $this->_check;
  }
  /**
   * Install the payment module and its configuration settings
   *
   */
  function install() {
    global $db;
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable ESPay Module', 'MODULE_PAYMENT_ESPAY_STATUS', 'True', 'Do you want to accept ESPay payments?', '6', '0', 'zen_cfg_select_option(array(\'True\', \'False\'), ', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Payment Key', 'MODULE_PAYMENT_ESPAY_MERCHID', '', 'The Payment Key used for the ESPay service', '6', '0', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Service Password', 'MODULE_PAYMENT_ESPAY_PASS', '', 'Password for ESPay service', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_ESPAY_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_ESPAY_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'zen_get_zone_class_title', 'zen_cfg_pull_down_zone_classes(', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_ESPAY_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'zen_cfg_pull_down_order_statuses(', 'zen_get_order_status_name', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Sandbox/Production Mode', 'MODULE_PAYMENT_ESPAY_MODE', 'SANDBOX', 'Determines if module is in testing mode.', '6', '0', 'zen_cfg_select_option(array(\'SANDBOX\', \'PRODUCTION\'), ', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Minimum Order Total', 'MODULE_PAYMENT_ESPAY_MIN_ORDER', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Order Total', 'MODULE_PAYMENT_ESPAY_MAX_ORDER', '0', '', '6', '0', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Credit Card Fee %', 'MODULE_PAYMENT_ESPAY_CREDIT_CARD_FEE_PERSEN', '0', '', '6', '0', now())");
    
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee BCA KlikPay', 'MODULE_PAYMENT_ESPAY_FEE_BCA', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Epay Bri', 'MODULE_PAYMENT_ESPAY_FEE_EPAY_BRI', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Mandiri Internet Banking', 'MODULE_PAYMENT_ESPAY_FEE_MANDIRI_IB', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Mandiri Ecash', 'MODULE_PAYMENT_ESPAY_FEE_MANDIRI_ECASH', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Credit Card', 'MODULE_PAYMENT_ESPAY_FEE_CREDIT_CARD', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Permata ATM', 'MODULE_PAYMENT_ESPAY_FEE_PERMATA_ATM', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Danamon OB', 'MODULE_PAYMENT_ESPAY_FEE_DANAMON_OB', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee DKI Internet Banking', 'MODULE_PAYMENT_ESPAY_FEE_DKI_IB', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee XL Tunai', 'MODULE_PAYMENT_ESPAY_FEE_XL_TUNAI', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee BII ATM', 'MODULE_PAYMENT_ESPAY_FEE_BII_ATM', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee BNI Debit Online', 'MODULE_PAYMENT_ESPAY_FEE_BNI_DEBIT_ONLINE', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Permata Net Pay', 'MODULE_PAYMENT_ESPAY_FEE_PERMATA_NET_PAY', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Nobupay', 'MODULE_PAYMENT_ESPAY_FEE_NOBUPAY', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Finpay', 'MODULE_PAYMENT_ESPAY_FEE_FINPAY', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Mandiri SM', 'MODULE_PAYMENT_ESPAY_FEE_MANDIRI_SM', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Mayapada Internet Banking', 'MODULE_PAYMENT_ESPAY_FEE_MAYAPADA_IB', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee MUAMALAT ATM', 'MODULE_PAYMENT_ESPAY_FEE_MUAMALAT_ATM', '0', '', '6', '0', now())");
    $db->Execute("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Transaction Fee Bitcoin', 'MODULE_PAYMENT_ESPAY_FEE_BITCOIN', '0', '', '6', '0', now())");
    
  }
  /**
   * Remove the module and all its settings
   *
   */
  function remove() {
    global $db;
    $db->Execute("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
  }
  /**
   * Internal list of configuration keys used for configuration of the module
   *
   * @return array
   */
  function keys() {
    return array('MODULE_PAYMENT_ESPAY_STATUS', 'MODULE_PAYMENT_ESPAY_MERCHID', 'MODULE_PAYMENT_ESPAY_PASS', 'MODULE_PAYMENT_ESPAY_SORT_ORDER', 'MODULE_PAYMENT_ESPAY_ZONE','MODULE_PAYMENT_ESPAY_ORDER_STATUS_ID', 'MODULE_PAYMENT_ESPAY_MODE','MODULE_PAYMENT_ESPAY_MIN_ORDER','MODULE_PAYMENT_ESPAY_MAX_ORDER','MODULE_PAYMENT_ESPAY_CREDIT_CARD_FEE_PERSEN','MODULE_PAYMENT_ESPAY_FEE_BCA','MODULE_PAYMENT_ESPAY_FEE_EPAY_BRI','MODULE_PAYMENT_ESPAY_FEE_MANDIRI_IB','MODULE_PAYMENT_ESPAY_FEE_MANDIRI_ECASH','MODULE_PAYMENT_ESPAY_FEE_CREDIT_CARD','MODULE_PAYMENT_ESPAY_FEE_PERMATA_ATM','MODULE_PAYMENT_ESPAY_FEE_DANAMON_OB','MODULE_PAYMENT_ESPAY_FEE_DKI_IB','MODULE_PAYMENT_ESPAY_FEE_XL_TUNAI','MODULE_PAYMENT_ESPAY_FEE_BII_ATM','MODULE_PAYMENT_ESPAY_FEE_BNI_DEBIT_ONLINE','MODULE_PAYMENT_ESPAY_FEE_PERMATA_NET_PAY','MODULE_PAYMENT_ESPAY_FEE_NOBUPAY','MODULE_PAYMENT_ESPAY_FEE_FINPAY','MODULE_PAYMENT_ESPAY_FEE_MANDIRI_SM','MODULE_PAYMENT_ESPAY_FEE_MAYAPADA_IB','MODULE_PAYMENT_ESPAY_FEE_MUAMALAT_ATM','MODULE_PAYMENT_ESPAY_FEE_BITCOIN'); //'MODULE_PAYMENT_IPAYMEX_METHOD'
  }
}
?>