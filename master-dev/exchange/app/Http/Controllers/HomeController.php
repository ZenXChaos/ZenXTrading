<?php namespace App\Http\Controllers;

class HomeController extends Controller {

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
	}

	/**
	 * Show the application dashboard to the user.
	 *
	 * @return Response
	 */
	public function index()
	{
		$sell_orders = \App\Exchange\Currencies\BTC_USD::where(array('way' => 'sell'))->orderBy('request_amount', 'desc')->take(25)->get();
		$buy_orders = \App\Exchange\Currencies\BTC_USD::where(array('way' => 'buy'))->orderBy('request_amount', 'desc')->take(25)->get();
		$my_orders = \App\Exchange\Currencies\BTC_USD::where(array('uid' => \Auth::user()->id))->orderBy('way', 'asc')->orderBy('request_amount', 'desc')->take(1000)->get();

		$usdfunds = \App\Exchange\FundSource::where(array('uid' => \Auth::user()->id, 'currency' => 'usd'))->orderBy('id', 'desc')->take(1)->first();
		return view('home', array('sell_orders' => $sell_orders, 'buy_orders' => $buy_orders, 'usdfunds' => $usdfunds, 'my_orders' => $my_orders ));
	}

}
