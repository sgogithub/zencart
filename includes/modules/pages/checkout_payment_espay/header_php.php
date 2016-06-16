<?php
$order_id = (!empty($_REQUEST['order_id'])?$_REQUEST['order_id']:'');
$passwordServer = (!empty($_REQUEST['password'])?$_REQUEST['password']:'');
$amt = (!empty($_REQUEST['amount'])?$_REQUEST['amount']:'0');
$product_code = (!empty($_REQUEST['product_code'])?$_REQUEST['product_code']:'0');
$payment_ref = (!empty($_REQUEST['payment_ref'])?$_REQUEST['payment_ref']:'0');
$passwordAdmin = MODULE_PAYMENT_ESPAY_PASS;

$select_order = $db->Execute("select * from " . TABLE_ORDERS . " where orders_id= '".$order_id."'");
$results = $select_order->fields['orders_id'];
$member_id = $select_order->fields['customers_id'];

$select_orders_status_history = $db->Execute("select * from " . TABLE_ORDERS_STATUS_HISTORY . " where orders_id= '".$order_id."'");
$results_orders_status_history = $select_orders_status_history->fields['orders_status_id'];

$select_orders_status = $db->Execute("select * from " . TABLE_ORDERS_STATUS . " where orders_status_id= '".$results_orders_status_history."'");
$select_orders_status = $select_orders_status->fields['orders_status_name'];

$select_orders_status_pay = $db->Execute("select * from " . TABLE_ORDERS_STATUS . " where orders_status_name= 'Processing'");
$select_orders_status_pay = $select_orders_status_pay->fields['orders_status_id'];

$select_orders_espay_history = $db->Execute("select * from orders_espay_history where orders_id= '".$order_id."'");
$select_orders_espay_history = $select_orders_espay_history->fields['payment_method'];

$select_orders_espay_history_cred = $db->Execute("select * from orders_espay_history where orders_id= '".$order_id."'");
$select_orders_espay_history_cred = $select_orders_espay_history_cred->fields['creditcard_fee'];

$espayproduct = $select_orders_espay_history;
$classes=explode(",",$espayproduct); 
$productName = $classes[0];
$bankCode = $classes[1];
$productCode = $classes[2];


if($productCode == 'CREDITCARD'){
	$select_order_total = $db->Execute("select * from " . TABLE_ORDERS_TOTAL . " where orders_id= '".$order_id."' and class='ot_subtotal'");
	$select_order_total->fields['value'];
}
else{
	$select_order_total = $db->Execute("select * from " . TABLE_ORDERS_TOTAL . " where orders_id= '".$order_id."' and class='ot_total'");
	$select_order_total->fields['value'];
}

$select_order_fee = $db->Execute("select * from " . TABLE_ORDERS_TOTAL . " where orders_id= '".$order_id."' and class='ot_transactionfee'");
$select_order_fee->fields['value'];

$select_order_creditcardfee = $db->Execute("select * from " . TABLE_ORDERS_TOTAL . " where orders_id= '".$order_id."' and class='ot_creditcardfee'");
$select_order_creditcardfee->fields['value'];

if($passwordAdmin != $passwordServer){
	$flagStatus = '1;Invalid Password;;;;;';
	echo $flagStatus;
}
else{
	if(count($results) < 1){
		$flagStatus = '1;Invalid Order Id;;;;;';
		echo $flagStatus;
	}
	else{
	//	echo date("d M Y H:i:s");die;
		   $order_id_ori =  $order_id;
		   $ccy = $select_order->fields['currency'];
		   $post_date = $select_order->fields['date_purchased'];
		   $time = substr($post_date,10,10);
		   $feeDb = $select_order_fee->fields['value'];
		   $creditcardfeeDb = $select_order_creditcardfee->fields['value'];
		   $amountOri = $select_order_total->fields['value'];
		   $post_date_format = date("d/m/Y",strtotime($post_date));
		   $datetimeformat = $post_date_format.''.$time;
		   
		   if($order_id_ori && $select_orders_status == 'Update'){
		   	 $flagStatus = '1,Failed,,,,';
		   }
		   elseif($order_id_ori && $select_orders_status == 'Delivered'){
		   	 $flagStatus = '1,Failed,,,,';
		   }
		   elseif($order_id_ori && $select_orders_status == 'Processing'){
		   	 $flagStatus = '1,Failed,,,,';
		   } 
		   elseif($order_id_ori && $select_orders_status == 'Waiting Payment'){
		   	 $flagStatus = '1,Failed,,,,';
		   }
		   else{
		   	if($productCode == 'CREDITCARD'){
				$totalamount = $amountOri +(( ($amountOri + $feeDb) * $select_orders_espay_history_cred)) / 100;
		   		$meta_value = $totalamount;
		   	}
		   	else{
		   	 	$meta_value = $amountOri-$feeDb;
		   	}
		   	
		   	 $reconcile_id = $member_id . " - " . $order_id_ori . date ( 'YmdHis' );
//		   	 $flagStatus = '0;Success;'.$order_id_ori.';'.$meta_value.';'.$ccy.';Payment '.$order_id_ori.';'.$datetimeformat.'';
		   	 $flagStatus = '0,Success,'.$reconcile_id.','.$order_id_ori.','.date ( 'Y-m-d H:i:s' ).'';
//		   	 $flagStatus = '0,Success,' . $reconcile_id . ',' . $order_id_ori . ',' . date ( 'Y-m-d H:i:s' ) . ''; punya delon
		   	  $comment = "Mohon tunggu maksimal 24 jam, pesanan anda sedang kami verifikasi mohon simpan bukti pembayaran Anda, jika diperlukan, tim kami akan menghubungi Anda untuk pengecekan lebih lanjut. ";
		   	 $db->Execute("UPDATE `".TABLE_ORDERS_STATUS_HISTORY."` SET orders_status_id = '".$select_orders_status_pay."',comments = '".$comment."' WHERE orders_id = '".$order_id_ori."'");
	    	 $db->Execute("UPDATE orders_espay_history SET orders_status_id = '".$select_orders_status_pay."' WHERE orders_id = '".$order_id_ori."'");
	    	 $db->Execute("UPDATE `".TABLE_ORDERS."` SET orders_status = '".$select_orders_status_pay."' WHERE orders_id = '".$order_id_ori."'");
	    
		   }
		   echo $flagStatus;
	}
}    
    
die;	    
?>
