<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::group(array('prefix' => 'Bitcoind'), function(){

	Route::group (array('prefix' => 'Wallet'), function(){

		Route::get("/GenerateAddress", 'BitcoindController@GenBitcoinWalletAddress');

		Route::get("/MyAddresses", 'BitcoindController@GrabMyAddresses');

		Route::get("/{wallet_address}/Delete", 'BitcoindController@DeleteWalletAddress');
	});

	Route::get('/', function(){ return "hi!" ; });

});


Route::group(array('prefix' => 'Ethereumd'), function(){

	Route::group (array('prefix' => 'Wallet'), function(){

		Route::get("/GenerateAddress", 'EthereumdController@GenEthereumWalletAddress');

		Route::get("/MyAddresses", 'EthereumdController@GrabMyAddresses');

		Route::get("/{wallet_address}/Delete", 'EthereumdController@DeleteWalletAddress');
	});

	Route::get('/', function(){ return "hi!" ; });

});

Route::get("/WalkChain/{uid}", '\App\BlockchainLite\Blockchain@WalkChain');

Route::group(array('prefix' => 'Funding'), function(){
	Route::group(array('prefix' => 'Paypal'), function(){
		Route::get("/PurchaseCurrency", '\App\Http\Controllers\PaymentGateway\PaypalController@PurchaseUSD');
	});
});


Route::group(array('prefix' => 'Exchange'), function(){
    Route::get("/{order_type}/Submit/{way}:{request_amount}@{bid}:{givetake}/", '\App\ZenX\ExchangeRequestController@Submit');
    Route::get("/CancelRequest/{id}", '\App\ZenX\ExchangeRequestController@Cancel');
});

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
