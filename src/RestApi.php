<?

use PayPal\Api\Payer;
use PayPal\Api\Item; 
use PayPal\Api\ItemList; 
use PayPal\Api\Details; 
use PayPal\Api\Amount; 
use PayPal\Api\Transaction; 
use PayPal\Api\RedirectUrls; 
use PayPal\Api\Payment; 
use PayPal\Exception\PayPalConnectionException; 

class paypalC{

	function __construct($returnUrl, $cancelUrl){		

		$this->returnUrl    = $returnUrl;
		$this->cancelUrl    = $cancelUrl;

		$this->showProducts = "網路訂單一筆";
		$this->showDesc     = "網路訂單一筆";
		$this->Currency     = "TWD";
	}

	public function checkOut($params = array()){
		global $payer, $item, $itemList, $details, $amount, $transaction, $redirectUrls, $payment, $paypal;

		if ($params == null){
            throw new Exception('Params are not set.');
        }

		$payer        = new Payer();
		$item         = new Item(); 
		$itemList     = new ItemList();
		$details      = new Details();
		$amount       = new Amount();
		$transaction  = new Transaction();
		$redirectUrls = new RedirectUrls();
		$payment      = new Payment();

		$payer->setPaymentMethod('paypal'); 

		$item->setName($this->showProducts)->setCurrency($this->Currency)->setQuantity(1)->setPrice($params['price']);

		$itemList->setItems(array($item));

		$details->setShipping($params['shipping'])->setTax('0.00')->setSubtotal($params['price']);

		$amount->setCurrency($this->Currency)->setTotal($params['total'])->setDetails($details);

		$transaction->setAmount($amount)->setItemList($itemList)->setDescription($this->showDesc)->setInvoiceNumber(uniqid());

		$redirectUrls->setReturnUrl($this->returnUrl)->setCancelUrl($this->cancelUrl);

		$payment->setIntent('sale')->setPayer($payer)->setRedirectUrls($redirectUrls)->setTransactions(array($transaction));

		try { 
			$payment->create($paypal);
		} catch (PayPalConnectionException $e) { 
			echo $e->getData();
			die();
		} 

		$approvalUrl = $payment->getApprovalLink();

		return $approvalUrl;
	}
}

?>