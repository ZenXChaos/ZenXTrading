<?php namespace App\Exchange\Currencies;

use Illuminate\Database\Eloquent\Model;

// Ƀitcoin to USD
class ETH_USD extends \App\Exchange\BaseCurrency {


	public function __construct(){
		$this->currency_model = "eth-usd";
	}

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'eth-usd_orders';

}
