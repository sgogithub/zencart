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
	
            <form method="POST" action="./">
				<?php
				$espayproduct = $_SESSION['paymentType'];
				$classes=explode(",",$espayproduct); 
				$productName = $classes[0];
				$bankCode = $classes[1];
				$productCode = $classes[2];
		        	
				$_POST['paymentType'] = 'Online Payment: '.$productName;
				$_POST['bankCode'] = $bankCode;
				$_POST['productName'] = $productName;
				$_POST['productCode'] = $productCode;
				
			    $urlserver = MODULE_PAYMENT_ESPAY_MODE == 'PRODUCTION'? 'https://kit.espay.id/public/signature/js' : 'http://sandbox-kit.espay.id/public/signature/js';
				$productCode = $_POST['productCode'];
			    $bankCode = $_POST['bankCode'];
			    $order_id_get = $zv_orders_id;
			    $payment_key = MODULE_PAYMENT_ESPAY_MERCHID;
			    $redirect = 'http://google.com';
			    if($productCode == 'PERMATAATM' || $productCode == 'MUAMALATATM' || $productCode == 'BIIATM'){ 
			    	if($select_orders_status == 'Waiting Payment'){
			    		?>
			    			<h3 id="checkoutSuccessThanks" class="centeredContent">
		    			 <div class="entry-content">
<!--							  <B><H4><img src="success.png">  Hi <b><?=$fullname?>,</b> </H4> </B><hr>-->
							  Order kamu telah kami terima! Kami harap kamu dapat menggunakan produk yang dipesan secepatnya dengan 
							  melakukan pembayaran via <u><?=$productName;?></u>.
							  <br><br>
							  Nomor Order/Id # Anda : <font color="red"><?=$zv_orders_id?> </font><br>
<!--							  Jumlah uang yang ditagihkan : <font color="red"><?=$ccy?>. <?=number_format($amount, 2); ?> </font>-->
							  <br><br>
							  <input name="post" class="cssButton submit_button button  button_update" onmouseover="this.className='cssButtonHover  button_update button_updateHover'" onmouseout="this.className='cssButton submit_button button  button_update'" type="submit" value="Continue Shopping" />
						  </div>
						  </h3>
			    		
			    		<?php 
			    	}
			    	elseif($select_orders_status == 'Processing'){
			    		?>
			    				<h3 id="checkoutSuccessThanks" class="centeredContent">
								  <div class="entry-content">
									  <B><H4>Selamat! Anda akan menerima pesanan anda dalam beberapa kali pengiriman</H4> </B><hr>
									  <!--<img src="success.png">   Hi <b><?='agung brahma arvin'?>,</b>--> <br>
									  Terima Kasih telah belanja di toko kami dengan menggunakan <u><?=$productName?> via ESPay Payment Gateways</u>.
									  <br><br>
									  Nomor Order/Id # Anda : <font color="red"><?=$zv_orders_id?> </font><br>
			<!--						  Jumlah uang yang ditagihkan : <font color="red"><?=$ccy?>. <?=number_format($amount_ori, 2); ?> </font>-->
									  <br><br>
			<!--								  Pembayaran via <?=$_REQUEST['method']?> sukses! -> -->
									  Kami akan segera memproses pesanan Anda dan mengatur pengiriman pesanan. 
									  <br><br>
									  <input name="post" class="cssButton submit_button button  button_update" onmouseover="this.className='cssButtonHover  button_update button_updateHover'" onmouseout="this.className='cssButton submit_button button  button_update'" type="submit" value="Continue Shopping" />
								  </div>		
								</h3>
			    		<?php 
			    	}
			    	else{
			    	?>
			    	<h3 id="checkoutSuccessThanks" class="centeredContent">
		    			 <div class="entry-content">
<!--							  <B><H4><img src="success.png">  Hi <b><?=$fullname?>,</b> </H4> </B><hr>-->
							  Order kamu telah kami terima! Kami harap kamu dapat menggunakan produk yang dipesan secepatnya dengan 
							  melakukan pembayaran via <u><?=$productName;?></u>.
							  <br><br>
							  Nomor Order/Id # Anda : <font color="red"><?=$zv_orders_id?> </font><br>
<!--							  Jumlah uang yang ditagihkan : <font color="red"><?=$ccy?>. <?=number_format($amount, 2); ?> </font>-->
							  <br><br>
							  <input name="post" class="cssButton submit_button button  button_update" onmouseover="this.className='cssButtonHover  button_update button_updateHover'" onmouseout="this.className='cssButton submit_button button  button_update'" type="submit" value="Continue Shopping" />
						  </div>
						  </h3>
			    	<?php 
			    	}
	        	}
	        	else{
			    
							if($select_orders_status == 'Waiting Payment'){
							?>
							<h3 id="checkoutSuccessThanks" class="centeredContent">
								  <div class="entry-content">
									  <B><H4>AN ERROR OCCURRED IN THE PROCESS OF PAYMENT</H4> </B><hr>
									  <font color="red">Order #<?=$zv_orders_id?> </font>.
									  <br>
									  Click <a href='./'>here </a>to continue shopping.
									  <br>
								  </div>		
								</h3>
							<?php }
							elseif($select_orders_status == 'Pending'){
							?>
								<h3 id="checkoutSuccessThanks" class="centeredContent">
								  <div class="entry-content">
									  <B><H4>AN ERROR OCCURRED IN THE PROCESS OF PAYMENT</H4> </B><hr>
									  <font color="red">Order #<?=$zv_orders_id?> </font>.
									  <br>
									  Click <a href='./'>here </a>to continue shopping.
									  <br>
								  </div>		
								</h3>
							<?php 
							}
							else{
							?>
								<h3 id="checkoutSuccessThanks" class="centeredContent">
								  <div class="entry-content">
									  <B><H4>Selamat! Anda akan menerima pesanan anda dalam beberapa kali pengiriman</H4> </B><hr>
									  <!--<img src="success.png">   Hi <b><?='agung brahma arvin'?>,</b>--> <br>
									  Terima Kasih telah belanja di toko kami dengan menggunakan <u><?=$productName?> via ESPay Payment Gateways</u>.
									  <br><br>
									  Nomor Order/Id # Anda : <font color="red"><?=$zv_orders_id?> </font><br>
			<!--						  Jumlah uang yang ditagihkan : <font color="red"><?=$ccy?>. <?=number_format($amount_ori, 2); ?> </font>-->
									  <br><br>
			<!--								  Pembayaran via <?=$_REQUEST['method']?> sukses! -> -->
									  Kami akan segera memproses pesanan Anda dan mengatur pengiriman pesanan. 
									  <br><br>
									  <input name="post" class="cssButton submit_button button  button_update" onmouseover="this.className='cssButtonHover  button_update button_updateHover'" onmouseout="this.className='cssButton submit_button button  button_update'" type="submit" value="Continue Shopping" />
								  </div>		
								</h3>
							<?php 
							} 
	        	}
							?>	
			</form>
</fieldset>
<?php
    }
?>
<!--eof -product notifications box-->
<?php if($select_orders_status == 'Waiting Payment'){?>
	<h3 id="checkoutSuccessThanks" class="centeredContent"><?php echo 'Please click the confirmation and payment for completing the above process'; ?></h3>
<?php }?>
</div>
