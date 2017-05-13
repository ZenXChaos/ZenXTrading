<?php namespace App\Http\Controllers\PaymentGateway;


use Illuminate\Auth\Authenticatable;

use \PayPal\Api\Details;
use \PayPal\Api\Item;
use \PayPal\Api\ItemList;
use \PayPal\Api\Payer;
use \PayPal\Api\Payment;
use \PayPal\Api\RedirectUrls;
use \PayPal\Api\Transaction;
use \PayPal\Rest\ApiContext;
use \PayPal\Api\CreditCard;

use \App\BlockchainLite\Blockchain;

use Illuminate\Http\Request;

class PaypalController extends \App\Http\Controllers\Controller {

	/*
	|--------------------------------------------------------------------------
	| Home Controller
	|--------------------------------------------------------------------------
	|
	| This controller renders your application's "dashboard" for users that
	| are authenticated. Of course, you are free to change or remove the
	| controller as you wish. It is just here to get your app started!
	|
	*/

	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');

        header("Content-type: application/json");
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function PurchaseUSD(Request $request)
	{
        $apiContext = null;
        
        // Process PayPal API Payment
        $apiContext = new \PayPal\Rest\ApiContext(
            new \PayPal\Auth\OAuthTokenCredential(
                '',     // ClientID
                ''      // ClientSecret
            )
        );
            

        $card = new \PayPal\Api\PaymentCard();
        $card->setType("visa")
            ->setNumber("4669424246660779")
            ->setExpireMonth("11")
            ->setExpireYear("2019")
            ->setCvv2("012")
            ->setFirstName("Joe")
            ->setBillingCountry("US")
            ->setLastName("Shopper");

        $fi = new \PayPal\Api\FundingInstrument();
        $fi->setPaymentCard($card);

        $payer = new \PayPal\Api\Payer();
        $payer->setPaymentMethod("credit_card")
            ->setFundingInstruments(array($fi));

        $item1 = new \PayPal\Api\Item();
        $item1->setName('USD')
            ->setDescription('USD-VIRTUAL, ZENX TRADING')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setTax(0.0)
            ->setPrice($request->USD);

        $itemList = new \PayPal\Api\ItemList();
        $itemList->setItems(array($item1));

        $details = new \PayPal\Api\Details();
        $details->setShipping(0)
            ->setTax(0)
            ->setSubtotal($request->USD);

        $amount = new \PayPal\Api\Amount();
        $amount->setCurrency("USD")
            ->setTotal($request->USD)
            ->setDetails($details);

        $transaction = new \PayPal\Api\Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("USD, ZENX TRADING - VCASH")
            ->setInvoiceNumber(uniqid());

        $payment = new \PayPal\Api\Payment();
        $payment->setIntent("sale")
            ->setPayer($payer)
            ->setTransactions(array($transaction));


        try {
            $payment->create($apiContext);
        } catch (Exception $ex) {

            die($ex->getData());
            //ResultPrinter::printError('Create Payment Using Credit Card. If 500 Exception, try creating a new Credit Card using <a href="https://www.paypal-knowledge.com/infocenter/index?page=content&widgetview=true&id=FAQ1413">Step 4, on this link</a>, and using it.', 'Payment', null, $request, $ex);
            exit(1);
        }


        if($payment->state == "approved"){ // If payment was approved

            $lastFunding = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'usd'))->orderBy('id', 'desc')->take(1)->get()->first();

            $newFunds = new \App\Exchange\FundSource();

            $newFunds->uid = \Auth::user()->id;
            $newFunds->currency = "usd";
            $newFunds->total_funds = +$request->USD;
            $newFunds->funds_remaining = $request->USD + ($lastFunding != null ? $lastFunding->funds_remaining : 0);
            $newFunds->previous_hash = ($lastFunding != null ? $lastFunding->hash : str_repeat("0",60));
            $newFunds->hash = hash('sha256', json_encode($newFunds));

            $newFunds->save();

            $blkdata = (array) $newFunds;

            $blkexists = file_exists("/var/www/blockchain/user_".$newFunds->uid.".dat");

            $te = json_encode( $blkdata );

            // Add to Blockchain
            \App\BlockchainLite\Blockchain::addblock("/var/www/blockchain/user_".$newFunds->uid.".dat", $te, !$blkexists);
        }

        return [];
                        
    }

    public static function transactions()
    {
        return \App\Exchange\FundSource::where('uid', \Auth::user()->id)->get();
    }

}
