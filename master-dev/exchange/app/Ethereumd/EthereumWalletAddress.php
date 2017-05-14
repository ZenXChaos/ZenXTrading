<?php namespace App\Ethereumd;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class EthereumWalletAddress extends Model {

	use Authenticatable;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $table = 'eth-wallet_addresses';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
    public function validator(array $data)
	{

        //Validate required Address fields
		return Validator::make($data, [
			'uid' => 'required',
            'wallet_address' => 'required',
			'confirmations' => 'required|min:0',
			'total_received' => 'required',
			'total_sent' => 'required',
			'balance' => 'required',
			'unconfirmed_balance' => 'required',
			'final_balance' => 'required',
			'n_tx' => 'required',
			'unconfirmed_n_tx' => 'required',
			'final_n_tx' => 'required',
			'tx_url' => 'required',
			'tx_hash' => 'required',
			'updated_at' => 'required'
		]);
	}

	protected $fillable =   ['uid', 'wallet_address', 'confirmations', 
                            'total_received', 'total_sent', 'balance', 
                            'unconfirmed_balance', 'final_balance', 'n_tx', 
                            'unconfirmed_n_tx', 'final_n_tx', 'tx_url', 
                            'tx_hash', 'updated_at', 'used'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	

    protected $hidden = [];

    public static function GetAddrInfo($addr)
    {
        return $addr_info = App\Bitcoind\EthereumWalletAddr::where('wallet_address', $addr)->first();
    }

}
