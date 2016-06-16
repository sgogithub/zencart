<?php
/**
 * Page Template
 *
 * Loaded automatically by index.php?main_page=checkout_success.<br />
 * Displays confirmation details after order has been successfully processed.
 *
 * @package templateSystem
 * @copyright Copyright 2003-2016 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: Author: DrByte  Mon Mar 23 13:48:06 2015 -0400 Modified in v1.5.5 $
 */
?>
<div class="centerColumn" id="checkoutSuccess">
<!--bof -gift certificate- send or spend box-->
<?php
// only show when there is a GV balance
  if ($customer_has_gv_balance ) {
?>
<div id="sendSpendWrapper">
<?php require($template->get_template_dir('tpl_modules_send_or_spend.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_modules_send_or_spend.php'); ?>
</div>
<?php
  }
?>
<!--eof -gift certificate- send or spend box-->


<!-- bof order details -->
<?php
$select_orders_status_history = $db->Execute("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id= '".$zv_orders_id."'");
$results_orders_status_history = $select_orders_status_history->fields['orders_status_id'];

$select_orders_status = $db->Execute("select * from " . TABLE_ORDERS_STATUS . " where orders_status_id= '".$results_orders_status_history."'");
$select_orders_status = $select_orders_status->fields['orders_status_name'];

if($select_orders_status == 'Waiting Payment'){
//	require($template->get_template_dir('tpl_account_history_info_default.php',DIR_WS_TEMPLATE, $current_page_base,'templates'). '/tpl_account_history_info_default.php');
}
?>
<!-- eof order details -->

<br class="clearBoth" />
<!--bof -product notifications box-->
<?php
/**
 * The following creates a list of checkboxes for the customer to select if they wish to be included in product-notification
 * announcements related to products they've just purchased.
 **/
    if ($flag_show_products_notification == true) {
?>
<fieldset id="csNotifications">
		<h3 id="checkoutSuccessThanks" class="centeredContent">
		  <div class="entry-content">
			  <B><H4>YOUR PAYMENT IS PROCCESSED!</H4> </B><hr>
			  <!--<img src="success.png">   Hi <b><?='agung brahma arvin'?>,</b>--> <br>
		  WE ARE REDIRECTING YOU TO THE PAYMENT PAGE.
		  <br><br>
		  Your Order # is : <b></b><font color="red"><?=$zv_orders_id?> </font></b><br>
		<!--						  Jumlah uang yang ditagihkan : <font color="red"><?=$ccy?>. <?=number_format($amount_ori, 2); ?> </font>-->
		  <br><br>
		<!--								  Pembayaran via <?=$_REQUEST['method']?> sukses! -> -->
		  Please Do Not <b>Close</b>, <b>Refresh</b>, or Click <b>Back</b> on this page 
		  <br>
		  </div>		
		</h3> 
</fieldset>

			   <?php 
//					ini_set( 'display_errors', false );
//					error_reporting( 0 );
			    	$espayproduct = $_SESSION['paymentType'];
					$classes=explode(",",$espayproduct); 
					$productName = $classes[0];
					$bankCode = $classes[1];
					$productCode = $classes[2];
			        	
					$_POST['paymentType'] = 'Online Payment: '.$productName;
					$_POST['bankCode'] = $bankCode;
					$_POST['productName'] = $productName;
					$_POST['productCode'] = $productCode;
					
				    $urlserver = MODULE_PAYMENT_ESPAY_MODE == 'PRODUCTION'? 'https://secure.sgo.co.id/public/signature/js' : 'http://secure-dev.sgo.co.id/public/signature/js';
					$productCode = $_POST['productCode'];
				    $bankCode = $_POST['bankCode'];
				    $order_id_get = $zv_orders_id;
				    $payment_key = MODULE_PAYMENT_ESPAY_MERCHID;
				    $redirect = zen_href_link(checkout_notif_espay, '', 'SSL', false);
				    ?>
				            <script type="text/javascript" src="<?php echo $urlserver;?>"></script>
				        	<script type="text/javascript">
				            window.onload = function () {
				                var data = {
				                    paymentId: '<?=$order_id_get?>', //ORDERID
				                    key: '<?=$payment_key?>',
				                    backUrl: encodeURIComponent ('<?=$redirect?>'),
				                    bankCode:'<?=$bankCode?>',
				                    bankProduct :'<?=$productCode?>'//'MAYAPADAIB'
				                },
				                sgoPlusIframe = document.getElementById("sgoplus-iframe");
				                if (sgoPlusIframe !== null) {
				                    sgoPlusIframe.src = SGOSignature.getIframeURL(data);
				                }
				                SGOSignature.receiveForm();
				            };
				       	 	</script>
				        	<iframe id="sgoplus-iframe" src="" scrolling="no" allowtransparency="true" frameborder="0" height="300"></iframe>
<?php
    }
?>
<!--eof -product notifications box-->
<?php if($select_orders_status == 'Waiting Payment'){?>
	<h3 id="checkoutSuccessThanks" class="centeredContent"><?php echo 'Please click the confirmation and payment for completing the above process'; ?></h3>
<?php }?>
</div>
