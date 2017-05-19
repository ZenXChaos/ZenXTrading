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
		$primaryfunds = null;
		$secondaryfunds = null;
		$enough = false;

		switch($request->order_type)
		{
			case "BTC-USD":
				$exchReq = new \App\Exchange\Currencies\BTC_USD();
				$enough = $way == "sell" ? $amount >= 0.1 : $amount >= 1;

				$primaryfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'btc'))->orderBy('id', 'desc')->take(1)->get()->first();


				$secondaryfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'usd'))->orderBy('id', 'desc')->take(1)->get()->first();

				break;

			case "ETH-USD":
				$exchReq = new \App\Exchange\Currencies\ETH_USD();
				$enough = $way == "sell" ? $amount >=0.1 : $amount >= 1;

				$primaryfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'eth'))->orderBy('id', 'desc')->take(1)->get()->first();


				$secondaryfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'usd'))->orderBy('id', 'desc')->take(1)->get()->first();
				break;

			case "BTC-ETH":
				$exchReq = new \App\Exchange\Currencies\BTC_ETH();
				$enough = $way == "sell" ? $amount >= 0.1 : $amount >= 0.1;

				$primaryfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'btc'))->orderBy('id', 'desc')->take(1)->get()->first();


				$secondaryfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'eth'))->orderBy('id', 'desc')->take(1)->get()->first();
				break;

			default:
				return null;
				break;
		}

		$updatedfunds = new \App\Exchange\FundSource();
		// Get|Set the order type
		switch($request->way)
		{
			case "sell": // User owns USD, wants Ƀitcoin

				if( $primaryfunds==null || $primaryfunds->funds_remaining == 0 )
				{
					return json_encode(array('Error'=>'No funds available'));
				}else if ( $primaryfunds->funds_remaining >= ($amount*$bid) )
				{
					$updatedfunds->uid = $primaryfunds->uid;
					$updatedfunds->currency = $primaryfunds->currency;
					$updatedfunds->total_funds = (-$amount*3);
					$updatedfunds->funds_remaining = $primaryfunds->funds_remaining - ($amount*$bid);
					$updatedfunds->previous_hash = "11";
					$updatedfunds->hash = "11";

					$updatedfunds->save();

					\App\BlockchainLite\Blockchain::addblock("/var/www/blockchain/user_".$updatedfunds->uid.".dat", $updatedfunds, false);

				}else{
					return json_encode(array('Error'=>'Insufficient funds'));
				}
				
				break;

			case "buy":

				if( $secondaryfunds==null || $secondaryfunds->funds_remaining == 0 )
				{
					return json_encode(array('Error'=>'No funds available'));
				}else if ( $secondaryfunds->funds_remaining >= ($amount*$bid) )
				{
					$updatedfunds->uid = $secondaryfunds->uid;
					$updatedfunds->currency = $secondaryfunds->currency;
					$updatedfunds->total_funds = (-$amount*$bid);
					$updatedfunds->funds_remaining = $secondaryfunds->funds_remaining - ($amount*$bid);
					$updatedfunds->previous_hash = "11";
					$updatedfunds->hash = "11";


					\App\BlockchainLite\Blockchain::addblock("/var/www/blockchain/user_".$updatedfunds->uid.".dat", $updatedfunds, false);

				}else{
					return json_encode(array('Error'=>'Insufficient funds'));
				}
				break;

			default:
				return null;
				break;
		}

		$enoughbid = $bid > 0.002;

		
		// Some validation
		if($uid < 0 || $way == null|| $enough == false || $enoughbid == false)
		{
			return json_encode(array('error'=>'Insufficient funds'));
		}

		$updatedfunds->save();

		// Set column values
		$exchReq->uid = $uid;
		$exchReq->way = $way;
		$exchReq->bid = $bid;
		$exchReq->request_amount = $amount;
		$exchReq->givetake = $givetake;
		$exchReq->align_market = $align_market;

		$exchReq->save(); // Save database entry
		
		return $exchReq;
		
	}

}
