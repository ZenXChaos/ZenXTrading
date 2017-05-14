<?php namespace App\ZenX;

use Illuminate\Auth\Authenticatable;
use App\Exchange\Currencies;
use App\Http\Controllers;
use Illuminate\Http\Request;

class ExchangeRequestController extends \App\Http\Controllers\Controller{



	/**
	 * Initialize Exchange Request.
	 *
	 */
	public function __construct()
	{
		//Authentication Required
		$this->middleware('auth');

        header("Content-type: application/json");
	}

    public function Cancel(Request $request)
    {
        // $request is created with POST_PARAMS

        $my_order = \App\Exchange\Currencies\BTC_USD::where(array('uid' => \Auth::user()->id, 'id' => $request->id))->get()->first();

        if($my_order == null){ // If order owned by authenticated user not found
            return null; // Return null
        }else{

			$funds = null;
			// Determine whether to return USD or BTC
			if($my_order->way == "sell")
			{
				$funds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'btc'))->get()->first();
			}else{
				$funds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'usd'))->get()->first();
			}


			$funds->total_funds = abs($my_order->request_amount*$my_order->bid); // Get absolute value of order cost
			$funds->funds_remaining = $funds->funds_remaining + $funds->total_funds; // Return balance
			$funds->save(); // Update balance

            $my_order->forceDelete(); // Delete the existing order

            return $funds; // Return new funds
        }

		return null;
    }

	// Create an exchange request
	// Submit order
	public function Submit(Request $request)
	{
		// $request is created with POST_PARAMS
		// $request->way = $_POST['way'], $_POST['amount'], $_POST['bid']

        $request->align_market = false;
		$exchReq = (object)[];
		$uid = \Auth::user()->id; // Get authenticated user id
		$amount = $request->request_amount; // Requested amount
		$way = $request->way; // Is trader buying or selling asset? (buy|sell)
		$givetake = $request->givetake; // Permissable price error margin. Wiling to sell/buy for $request->givetake plus or minus. 
		$align_market = $request->align_market; // Should price always align with market value ?
		$bid = $request->bid;
		$usdfunds = null;

		switch($request->order_type)
		{
			case "BTC-USD":
				$exchReq = new \App\Exchange\Currencies\BTC_USD();
				break;

			case "ETH-USD":
				$exchReq = new \App\Exchange\Currencies\ETH_USD();
				break;

			default:
				return null;
				break;
		}

		// Get|Set the order type
		switch($request->way)
		{
			case "buy": // User owns USD, wants Ƀitcoin
				$exchReq = new \App\Exchange\Currencies\BTC_USD();
				$enough = $amount > 1; // Min USD

				$usdfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'usd'))->orderBy('id', 'desc')->take(1)->get()->first();

				if( $usdfunds==null || $usdfunds->funds_remaining == 0 )
				{
					return json_encode(array('Error'=>'No funds available'));
				}else if ( $usdfunds->funds_remaining >= ($amount*$bid) )
				{
					$updatedfunds = new \App\Exchange\FundSource();
					$updatedfunds->uid = $usdfunds->uid;
					$updatedfunds->currency = $usdfunds->currency;
					$updatedfunds->total_funds = (-$amount*3);
					$updatedfunds->funds_remaining = $usdfunds->funds_remaining - ($amount*$bid);
					$updatedfunds->previous_hash = "11";
					$updatedfunds->hash = "11";

					$updatedfunds->save();

					\App\BlockchainLite\Blockchain::addblock("/var/www/blockchain/user_".$updatedfunds->uid.".dat", $updatedfunds, false);

				}else{
					return json_encode(array('Error'=>'Insufficient funds'));
				}
				
				break;

			case "sell":
				$exchReq = new \App\Exchange\Currencies\BTC_USD();
				$enough = $amount > 0.1; // Min ɃTC

				$usdfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'btc'))->orderBy('id', 'desc')->take(1)->get()->first();

				if( $usdfunds==null || $usdfunds->funds_remaining == 0 )
				{
					return json_encode(array('Error'=>'No funds available'));
				}else if ( $usdfunds->funds_remaining >= ($amount*$bid) )
				{
					$updatedfunds = new \App\Exchange\FundSource();
					$updatedfunds->uid = $usdfunds->uid;
					$updatedfunds->currency = $usdfunds->currency;
					$updatedfunds->total_funds = (-$amount*$bid);
					$updatedfunds->funds_remaining = $usdfunds->funds_remaining - ($amount*$bid);
					$updatedfunds->previous_hash = "11";
					$updatedfunds->hash = "11";

					$updatedfunds->save();

					\App\BlockchainLite\Blockchain::addblock("/var/www/blockchain/user_".$updatedfunds->uid.".dat", $updatedfunds, false);

				}else{
					return json_encode(array('Error'=>'Insufficient funds'));
				}
				return null;
				break;

			default:
				return null;
				break;
		}

		$enoughbid = $bid > 0.002;
		
		// Some validation
		if($uid < 0 || $way == null|| $align_market == null || $enough == false || $enoughbid == false)
		{
			return json_encode(array('error'=>'Insufficient funds'));
		}

		// Set column values
		$exchReq->uid = $uid;
		$exchReq->way = $way;
		$exchReq->request_amount = $amount;
		$exchReq->givetake = $givetake;
		$exchReq->align_market = $align_market;

		$exchReq->save(); // Save database entry
		
		return $exchReq;
		
	}

}
