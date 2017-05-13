<?php

namespace App\Http\Controllers;

 
//use BlockCypher\Api\Wallet;
use BlockCypher\Client\AddressClient;
use BlockCypher\Auth\SimpleTokenCredential;
use BlockCypher\Rest\ApiContext;
use Illuminate\Http\Request;
use App\Bitcoind;
//use BlockCypher\Client\WalletClient;

class BitcoindController extends \App\Http\Controllers\Controller {

	/*
	|--------------------------------------------------------------------------
	| Bitcoind Controller
	|--------------------------------------------------------------------------
	|
	| This controller contains most of the Bitcoin(wallet) functionality.
	| 
	*/

	/**
	 * Initialize Wallet.
	 *
	 */
	public function __construct()
	{
		//Authentication Required
		$this->middleware('auth');

        header("Content-type: application/json");
	}

	/**
	 * Wallet Funds Management
	 *
	 * These set of functions handle tasks such as Wallet Creation, Balance Inqueries, and other useful tasks specific to the Bitcoin Wallet.
	 */
	public function GenBitcoinWalletAddress(Request $request)
	{
		//First require that user is authenticated
		$this->middleware('auth');

		$apiContext = \BlockCypher\Rest\ApiContext::create(
                        'test3', 'btc', 'v1', new \BlockCypher\Auth\SimpleTokenCredential(''),
                        array('log.LogEnabled' => true, 'log.FileName' => 'BlockCypher.log', 'log.LogLevel' => 'DEBUG')
                );
                
		$addressClient = new \BlockCypher\Client\AddressClient($apiContext);
        $data = $addressClient->generateAddress();

		$btcblock = (array) json_decode($data, true); // Convert JSON to array
		$btcblock = (object) $btcblock; //Convert array to object

		$address = $addressClient->get($btcblock->address);

		$address->txrefs = $address->txrefs != null ? (object) $address->txrefs : null ;


		$bwa = new \App\Bitcoind\BitcoinWalletAddress(); // Create new Ƀitcoin Wallet->Address Endpoint object
		$bwa->wallet_id = 0;
		$bwa->wallet_address = $address->address; // Wallet->Address identifier. Not to be confused with Wallet Address.

		if(sizeof($address->txrefs) > 0 ){
			$bwa->confirmations = $address->txrefs->confirmations ; // Confirmations will not be available immediately
			$bwa->tx_hash = $address->txrefs->tx_hash; // A tx_hash won't be available immediately
		}else{
			$bwa->confirmations = 0 ; // Confirmations will not be available immediately
			$bwa->tx_hash = ""; // A tx_hash won't be available immediately
		}

		$bwa->total_received = $address->total_received; // Total Ƀitcoin received
		$bwa->total_sent = $address->total_sent; // Total Ƀitcoin sent
		$bwa->balance = $address->balance; // Total current balance
		$bwa->unconfirmed_balance = $address->unconfirmed_balance; // Unconfirmed balance
		$bwa->final_balance = $address->final_balance; // Total available balance
		$bwa->n_tx = $address->n_tx; 
		$bwa->unconfirmed_n_tx = $address->unconfirmed_n_tx;

		$bwa->used = $bwa->total_sent + $bwa->total_received + $bwa->unconfirmed_balance + $bwa->balance  != 0; // Has address received or sent any Ƀitcoin?
		$bwa->save();

		$bwa->privatekey = $data->privatekey;
		return json_encode(json_decode($bwa,1));
	}

	public function GenBitcoinWalletAddress_v2(){
		$btcblock = GenBitcoinWalletAddress();
		return view('gen-address', ['address'=>$btcblock['address'], 'privatekey' => $btcblock['private'], 'publickey' => $btcblock['public'], 'privatekey_wif' => $btcblock['wif']]);
	}

	public function DeleteWalletAddress ($wallet_address)
	{

		$h = \App\Bitcoind\BitcoinWallet::find(3)->addys;

		var_dump($h);



		die();
		// Find the wallet->address bound to 
		$wallet_addr = \App\Bitcoind\BitcoinWalletAddress::where('wallet_address', $wallet_address)->first()->wallet;

		if($wallet_addr==null){
			return json_encode(array('action'=>'delete:wallet_'.$wallet_address, 'action_status'=>'fail', 'reason'=>'Wallet->Address does not exist!')); // Wallet->Address cannot be found
		}
		
		// Get the owner of the wallet
		$owner = $wallet_addr->owner;

		// Compare owner to logged in user
		if($owner->id!=Auth::user()->id){
			return json_encode(array('action'=>'delete:wallet_'.$wallet_address, 'action_status'=>'fail', 'reason'=>'Permission denied!')); // Probably does not own the wallet->address
		}else{
			$wallet_addr->forceDelete(); // Delete the record
		}
		
		return json_encode(array('action'=>'delete:wallet_'.$wallet_address, 'action_status'=>'success'));
	}


}
