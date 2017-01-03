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
	
	protected $liveAction = "https://www.paypal.com/cgi-bin/webscr";
    protected $testAction = "https://www.sandbox.paypal.com/cgi-bin/webscr";

	function __construct($account, $returnUrl, $notifiUrl, $cancelUrl, $mode = false){
        $this->Account   = $account;
        $this->returnUrl = $returnUrl;
        $this->notifiUrl = $notifiUrl;
        $this->cancelUrl = $cancelUrl;

        $this->testMode  = $mode;
	}

    protected function setActionMode(){
        return $this->testMode ? $this->testAction : $this->liveAction;
    }

	#秀出Items後付款
	public function checkOut($orderArr){

        if ($orderArr == null){
            throw new Exception('order are not set.');
        }

		$Html  = '<form name="dataForm" action="'.$this->setActionMode().'" method="post">';   
		$Html .= '<input type="hidden" name="cmd" value="_xclick">';
		$Html .= '<input type="hidden" name="business" value="'.$this->Account.'">';
		$Html .= '<input type="hidden" name="return" value="'.$this->returnUrl.'">';
        $Html .= '<input type="hidden" name="item_name" value="網路訂單一筆">';
		$Html .= '<input type="hidden" name="amount" value="'.$orderArr['orderAmount'].'">';
		$Html .= '<input type="hidden" name="invoice" value="'.$orderArr['orderId'].'">';
		$Html .= '<input type="hidden" name="charset" value="utf-8">';
		$Html .= '<input type="hidden" name="no_shipping" value="1">';
		$Html .= '<input type="hidden" name="no_note" value="">';
		$Html .= '<input type="hidden" name="currency_code" value="TWD">';
		$Html .= '<input type="hidden" name="notify_url" value="'.$this->notifiUrl.'">';
		$Html .= '<input type="hidden" name="rm" value="2">';
		$Html .= '<input type="hidden" name="cancel_return" value="'.$this->cancelUrl.'">';
		$Html .= '</form>';
        $Html .= '<script>document.dataForm.submit();</script>';

        echo $Html;
	}

    public function resPond(){

        $req = 'cmd=_notify-validate';
        foreach ($_POST as $key => $value){
            $value = urlencode(stripslashes($value));
            $req .= "&$key=$value";
        }

        // post back to PayPal system to validate
        $header = "POST /cgi-bin/webscr HTTP/1.0\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($req) ."\r\n\r\n";
        $fp = fsockopen ('www.paypal.com', 80, $errno, $errstr, 30);

        // assign posted variables to local variables
        $item_name        = $_POST['item_name'];
        $item_number      = $_POST['item_number'];
        $payment_status   = $_POST['payment_status'];
        $payment_amount   = $_POST['mc_gross'];
        $payment_currency = $_POST['mc_currency'];
        $txn_id           = $_POST['txn_id'];
        $receiver_email   = $_POST['receiver_email'];
        $payer_email      = $_POST['payer_email'];
        $order_sn         = $_POST['invoice'];
        $memo             = !empty($_POST['memo']) ? $_POST['memo'] : '';
        $action_note      = $txn_id . $memo;

    }
}

?>