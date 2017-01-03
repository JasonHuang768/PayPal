<?
/**
 *	This file is part of Paypal.
 *
 * @author JasonHuang <>
 *
 * @package Paypal
 * @since Paypal Ver 1.0
**/

namespace payment\Paypal;

class paypal{
	
    #Signature [Name-Value Pair] 
    protected $SN_liveEndpoints = "https://api-3t.paypal.com/nvp";
    protected $SN_testEndpoints = "https://api-3t.sandbox.paypal.com/nvp";

    #postEndpoints
	protected $liveEndpoints = "https://www.paypal.com/cgi-bin/webscr";
	protected $testEndpoints = "https://www.sandbox.paypal.com/cgi-bin/webscr";

	function __construct($actionArr, $mode = false){

        foreach ($actionArr as $k => $v) {
            if ($v == null){
                throw new Exception( $k.' are not set.');
            }
        }

        $this->userName       = $actionArr['username'];
        $this->passWord       = $actionArr['password'];
        $this->paypalCode     = $actionArr['paypalcode'];
        $this->returnUrl      = $actionArr['returnUrl'];
        $this->calcelUrl      = $actionArr['calcelUrl'];
        
        $this->testMode       = $mode;

        $this->currencyType = "TWD";
        $this->paymentType  = "Sale";
	}

    #Set checkout parameters
	public function checkOut($orderArr){

        foreach ($orderArr as $k => $v) {
            if ($v == null){
                throw new Exception( $k.' are not set.');
            }
        }

		$token = '';

        $returnURL = urlencode($this->returnUrl.'?currencyCodeType='.$this->currencyType.'&paymentType='.$this->paymentType.'&paymentAmount='.$orderArr['orderAmount'].'&invoice='.$orderArr['orderId']);

        $cancelURL = urlencode($this->calcelUrl."?orderId=".$orderArr['orderId'] );

        $nvpstr = "&Amt=".$orderArr['orderAmount']."&PAYMENTACTION=".$this->paymentType."&ReturnUrl=".$returnURL."&CANCELURL=".$cancelURL ."&CURRENCYCODE=".$this->currencyType;

        $resArray = $this->hashCode("SetExpressCheckout", $nvpstr);

        if(isset($resArray["ACK"])){
            $ack = strtoupper($resArray["ACK"]);
        }
        
        if (isset($resArray["TOKEN"])){
            $token = urldecode($resArray["TOKEN"]);
        }

        $payPalURL = $this->setActionMode()."&cmd=_express-checkout&token=".$token;

        return $payPalURL;

        // header("Location: $payPalURL");
	}

	#Complete payment
	public function respond($postData = array()){
		$order_sn = $postData['invoice'];
		$token    = urlencode( $postData['token']);
		$nvpstr   = "&TOKEN=".$token;
		$resArray = $this->hashCode("GetExpressCheckoutDetails", $nvpstr);
		$ack      = strtoupper($resArray["ACK"]);

        if($ack == "SUCCESS"){
			$token         = urlencode($postData['token']);
			$paymentAmount = urlencode($postData['paymentAmount']);
			$paymentType   = urlencode($postData['paymentType']);
			$currCodeType  = urlencode($postData['currCodeType']);
			$payerID       = urlencode($postData['PayerID']);
			$serverName    = urlencode($_SERVER['SERVER_NAME']);

            $nvpstr = '&TOKEN='.$token.'&PAYERID='.$payerID.'&PAYMENTACTION='.$paymentType.'&AMT='.$paymentAmount.'&CURRENCYCODE='.$currCodeType.'&IPADDRESS='.$serverName ;

            $resArray = $this->hashCode("DoExpressCheckoutPayment", $nvpstr);
            
            $ack = strtoupper($resArray["ACK"]);
            if($ack == "SUCCESS"){
                /* change order status */
                order_paid($order_sn, 2);
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
	}

	private function hashCode($methodName, $nvpStr){
        $version = '124.0';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->setAuthenticationEndpoints());
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POST, 1);

        $nvpreq = "METHOD=".urlencode($methodName)."&VERSION=".urlencode($version)."&PWD=".urlencode($this->passWord)."&USER=".urlencode($this->userName)."&SIGNATURE=".urlencode($this->paypalCode).$nvpStr;

        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        $response = curl_exec($ch);

        $nvpResArray = $this->deformatNVP($response);
        
        $nvpReqArray = $this->deformatNVP($nvpreq);

        // if (curl_errno($ch)){
        //     $_SESSION['curl_error_no'] = curl_errno($ch) ;
        //     $_SESSION['curl_error_msg'] = curl_error($ch);
        // }else{
        //     curl_close($ch);
        // }

        return $nvpResArray;
    }

    private function deformatNVP($nvpstr){

        $intial=0;
        $nvpArray = array();

        while(strlen($nvpstr)){
			$keypos                       = strpos($nvpstr, '=');
			$valuepos                     = strpos($nvpstr, '&') ? strpos($nvpstr, '&'): strlen($nvpstr);
			$keyval                       = substr($nvpstr, $intial, $keypos);
			$valval                       = substr($nvpstr, $keypos+1, $valuepos-$keypos-1);
			$nvpArray[urldecode($keyval)] = urldecode($valval);
			$nvpstr                       = substr($nvpstr, $valuepos+1, strlen($nvpstr));
        }

        return $nvpArray;
    }

    protected function setActionMode(){
        return $this->testMode ? $this->testEndpoints : $this->liveEndpoints;
    }

    protected function setAuthenticationEndpoints(){
        return $this->testMode ? $this->SN_testEndpoints : $this->SN_liveEndpoints;
    }
}

?>