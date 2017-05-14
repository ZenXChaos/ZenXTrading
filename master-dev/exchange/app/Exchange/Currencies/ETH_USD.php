<?php namespace App\Exchange\Currencies;

use Illuminate\Database\Eloquent\Model;

// Ƀitcoin to USD
class BTC_USD extends \App\Exchange\BaseCurrency {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'eth-usd_orders';

}
