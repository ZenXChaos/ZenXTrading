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

	// Delete an exchange request
	// Delete order
	public function Remove(Request $request)
	{
		$order = \App\Exchange\BaseCurrency::where('id', $request->id)->first();

		if($order!= null)
		{
			// If authenticated user owns order
			if($order->owner->id == Auth::user()->id)
			{
				$order->forceDelete(); // Delete the order
				return $order;
			}else{
				return null;
			}
		}
	}

    public function Cancel(Request $request)
    {
        // $request is created with POST_PARAMS
        // $request->way = $_POST['id']

        $my_order = \App\Exchange\Currencies\BTC_USD::where(array('uid' => \Auth::user()->id, 'id' => \Auth::user()->id))->orderBy('way', 'asc')->orderBy('request_amount', 'desc')->get()->first();

        if($my_order == null){
            return null;
        }else{
            $my_order->forceDelete();

            return $my_order;
        }
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
