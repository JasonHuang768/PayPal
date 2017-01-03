<?

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

?>