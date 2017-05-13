<?php namespace App\Exchange;

use Illuminate\Database\Eloquent\Model;

class BaseCurrency extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	//protected $table = 'xx-xx_orders';

	protected $rules = array('way' => 'in:buy|in:sell'); // Require `way` = sell|buy

	public function owner() // Owner of currency
    {
        return $this->hasOne('App\User', 'id', 'uid');
    }

    public function validator(array $data) // Validate required columns
	{
        //Validate required Address fields
		return Validator::make($data, [
            'uid' => 'required|min:0', // Owner ID
            'way' => 'required', // 'buy', 'sell'
			'request_amount' => 'required|min:0', // Amount of (asset) requested
			'bid' => 'required|min:0', // Max amount willing to pay.
			'filled' => 'required|min:0' // Amount filled
		]);
	}

	protected $fillable = ['uid', 'way', 'request_amount', 'givetake', 'align_market', 'filled'];

	protected $hidden = [];

}
