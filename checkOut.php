<?
$payment    = $_REQUEST['ChoosePayment'];
$Price      = $_REQUEST['Price'];
$Shipping   = $_REQUEST['Shipping'];
$totalPrice = $_REQUEST['Price'] + $_REQUEST['Shipping'];

switch ($payment) {
	case 'normal':

		$user = $_REQUEST['user'];
		$returnUrl = $_SERVER['HTTP_REFERER'].$_REQUEST['ResultUrl'];
		$notifyUrl = $_SERVER['HTTP_REFERER'].$_REQUEST['NotifyUrl'];
		$cancelUrl = $_SERVER['HTTP_REFERER'].$_REQUEST['CancelUrl'];

		require_once "src/Normal.php";
		$pp = new payment\Paypal\paypal($user, $returnUrl, $notifyUrl, $cancelUrl , true);

		break;
	case 'vnp_soap':

		$actionArr = array(
			"username"       => $_REQUEST['username'],
			"password"       => $_REQUEST['password'],
			"paypalcode"     => $_REQUEST['paypalcode'],
			"returnUrl"      => $_SERVER['HTTP_REFERER'].$_REQUEST['ResultUrl'],
			"calcelUrl"      => $_SERVER['HTTP_REFERER'].$_REQUEST['CancelUrl'],
		);

		require_once "src/VnpSoap.php";
		$pp = new payment\Paypal\paypal($actionArr, true);

		break;
	case 'restapi':
		require_once "vendor/autoload.php";
		require_once "src/RestApi.php";

		$clientID  = $_REQUEST['clientID'];
		$secretID  = $_REQUEST['secretID'];
		$returnUrl = $_SERVER['HTTP_REFERER'].$_REQUEST['ResultUrl'].'?success=true';
		$cancelUrl = $_SERVER['HTTP_REFERER'].$_REQUEST['CancelUrl'].'?success=false';
		$mode      = 'sandbox';

		$paypal = new PayPal\Rest\ApiContext(new PayPal\Auth\OAuthTokenCredential($clientID, $secretID));

		$paypal->setConfig(array(
		    'mode'                   => $mode, #sandbox, live
		    'http.ConnectionTimeOut' => 30,
		    'log.LogEnabled'         => false,
		    'log.Filename'           => '',
		    'log.Loglevel'           => 'FINE', #DEBUG, FINE
		    'validation.level'       => 'log' #log, strict, disable
		));

		$pp = new paypalC($returnUrl, $cancelUrl);
		break;
}

switch ($payment) {
	case 'normal':
		$orderArr = array(
		    "orderId"     => "order".date("ymdhis"),
		    "orderAmount" => $totalPrice,
		);

		$pp->checkOut($orderArr);
		break;
	case 'vnp_soap':
		$orderArr = array(
		    "orderId"     => "order".date("ymdhis"),
		    "orderAmount" => $totalPrice,
		);

		$url = $pp->checkOut($orderArr);

		header("Location: $url");
		break;
	case 'restapi':
		$orderArr = array(
			'price'    => $Price,
			'shipping' => $Shipping,
			'total'    => $totalPrice,
		);
		$paypalUrl = $pp->checkOut($orderArr);

		$paypaArr = explode("&token=", $paypalUrl);

		$token = array_pop($paypaArr);

		#存資料庫
		#$sql = "update ".$db->prefix("order")." set token = '".$token."' where o_id='".$this->o_id."'";
		#$db->query($sql);

		header("Location: {$paypalUrl}");

		break;
}



?>