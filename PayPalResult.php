<?

/**
 * Normal
 * Array
	(
	    [transaction_subject] => 
	    [txn_type] => web_accept
	    [payment_date] => 18:13:29 Nov 06, 2016 PST
	    [last_name] => Huang
	    [residence_country] => US
	    [pending_reason] => unilateral
	    [item_name] => 
	    [payment_gross] => 
	    [mc_currency] => TWD
	    [business] => Jason79706@gmail.com
	    [payment_type] => instant
	    [protection_eligibility] => Ineligible
	    [verify_sign] => AFcWxV21C7fd0v3bYYYRCpSSRl31AXSpbGwZpZSs1s9.XO5fGV9Fd.os
	    [payer_status] => verified
	    [test_ipn] => 1
	    [tax] => 0
	    [payer_email] => test@allmarketing.com.tw
	    [txn_id] => 5KK681045C774913S
	    [quantity] => 1
	    [receiver_email] => Jason79706@gmail.com
	    [first_name] => Jason
	    [invoice] => order161107101305
	    [payer_id] => NWGGWZD39LKSQ
	    [item_number] => 
	    [handling_amount] => 0
	    [payment_status] => Pending
	    [shipping] => 0
	    [mc_gross] => 100
	    [custom] => 
	    [charset] => utf-8
	    [notify_version] => 3.8
	    [auth] => Acy5LYoOqVH9gYbiWaIErnhJ.EGNUWyR6iNdJnq.mA96UsZ3RjDY94bsILgLjXM61ZDj6TZPNLB6dIPWeU1jANg
	)

	$transactionData = $_POST;

	print_r ($transactionData);
 */


/**
 * NVP / SOAP
 * Array
	(
	    [code] => paypal_ec
	    [currencyCodeType] => TWD
	    [paymentType] => Sale
	    [paymentAmount] => 100
	    [invoice] => order161107120255
	    [token] => EC-4MP21960W5025842A
	    [PayerID] => NWGGWZD39LKSQ
	)

	$transactionData = $_GET;

	print_r ($transactionData);
*/

/*

require_once "config/config.php";

use PayPal\Api\Payment; 
use PayPal\Api\PaymentExecution; 

$paymentID = $_GET['paymentId'];
$token     = $_GET['token'];
$payerId   = $_GET['PayerID'];

$payment   = Payment::get($paymentID, $paypal);
$execute   = new PaymentExecution();
$execute->setPayerId($payerId);

try{
	$result = $payment->execute($execute, $paypal); 
}catch(Exception $e){     
	die($e); 
} 

echo '付款成功！';

*/

?>