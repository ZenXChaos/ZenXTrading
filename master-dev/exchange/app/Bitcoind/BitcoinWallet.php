<?php namespace App\Bitcoind;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;

class BitcoinWallet extends Model {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	public $table = 'btc-wallets';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */


    public function validator(array $data)
	{
        //Validate required Address fields
		return Validator::make($data, [
            'uid' => 'required|min:0',
            'wallet_token' => 'required',
			'wallet_address' => 'required|min:0'
		]);
	}

	protected $fillable = ['uid', 'wallet_token', 'wallet_address'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['n_tx', 'unconfirmed_n_tx', 'final_n_tx'];

    public function owner(){
        return $this->hasOne('App\User', 'id', 'uid');
    }

    public function addys(){
        return $this->hasMany('App\Bitcoind\BitcoinWalletAddress', 'wallet_id', 'id');
    }

    public static function GetMyWallets($getAddys = false)
    {
        $wallet_multi_addrs = App\BitcoinWallet::where('uid', Auth::user()->id)->orderBy('id', 'desc')->get(); 

        if($getAddys){
            $walletData = array();
            foreach($wallet_multi_addrs as $wallet_addr)
            {
                $wallet_addr["Addresses"] = App\BitcoinWalletAddress::where('id', $wallet_addr["id"])->orderBy('id', 'desc')->get();
                array_push($walletData, $wallet_addr);
            }

            return $walletData;
        }

        return $wallet_multi_addrs;
    }

    public static function GetWalletAddresses($wallet_id)
    {

    }

}
