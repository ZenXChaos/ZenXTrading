<?php namespace App\Exchange\Currencies;

use Illuminate\Database\Eloquent\Model;

// Ƀitcoin to USD
class BTC_ETH extends \App\Exchange\BaseCurrency {


	public function __construct(){
		$this->currency_model = "btc-eth";
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'btc-eth_orders';

}
