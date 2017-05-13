<?php

namespace App\Http\Controllers\UnusedWallet;
 
use BlockCypher\Client\AddressClient;
use BlockCypher\Auth\SimpleTokenCredential;
use BlockCypher\Rest\ApiContext;
use App\Bitcoind\BitcoinWalletAddress;

class UnusedWalletController extends \App\Http\Controllers\Controller {

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
		//No Authentication Required Here
		//$this->middleware('auth');
	}

	/**
	 * Wallet Funds Management
	 *
	 * These set of functions handle tasks such as Wallet Creation, Balance Inqueries, and other useful tasks specific to the Bitcoin Wallet.
	 */
	public function CheckUnusedWallets()
	{
		
    }

}
