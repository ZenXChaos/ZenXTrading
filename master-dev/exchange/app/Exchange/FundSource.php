<?php namespace App\Exchange;

use Illuminate\Database\Eloquent\Model;

class FundSource extends Model {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user_funds';

	public function owner() // Owner of currency
    {
        return $this->hasOne('App\User', 'id', 'uid');
    }

    public function validator(array $data) // Validate required columns
	{
        //Validate required Address fields
		return Validator::make($data, [
            'uid' => 'required|min:0', // Owner ID
            'currency' => 'required', // 'buy', 'sell'
			'total_funds' => 'require', // Amount of (asset) requested
			'funds_remaining' => 'required', // Max amount willing to pay.
			'previous_hash' => 'required', // Amount filled
			'hash' => 'required' // Amount filled
		]);
	}

	protected $fillable = ['uid', 'current', 'total_funds', 'funds_remaining', 'previous_hash', 'hash', 'created_at', 'updated_at'];

	protected $hidden = [];

}
