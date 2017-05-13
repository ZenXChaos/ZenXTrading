<?php

namespace App\BlockchainLite;

use App\Bitcoind;

class Blockchain extends \App\Http\Controllers\Controller {

	/*
	|--------------------------------------------------------------------------
	| Blockchain-Lite Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles most core functionality for Blockchain-Lite
    | Blockchain-Lite is a (php-based) lite version of the blockchain.
	| In a nutshell, it provides an extra layer of security protecting data.
	| 
	*/

    public $magic = "0xD5E8A97F";
    public $hashalg = "sha256";
    public $hashlen = 32;
    public static function blksize(){
		return (new \App\BlockchainLite\Blockchain())->hashlen + 13;
	}

	public static function addblock($fn,$data,$genesis=FALSE) {
		$indexfn = $fn.'.idx';
		if (!$genesis) {
			if (!file_exists($indexfn)) return('Missing blockchain index file!');
			// get disk location of last block from index
			if (!$ix = fopen($indexfn, 'r+b')) return("Can't open ".$indexfn);
			$maxblock = unpack('V', fread($ix,4))[1];
			$zpos = (($maxblock*8)-4);
			fseek($ix, $zpos, SEEK_SET);
			$ofs = unpack('V', fread($ix, 4))[1];
			$len = unpack('V', fread($ix, 4))[1];
			// read last block and calculate hash
			if (!$bc = fopen($fn,'r+b')) return("Can't open ".$fn);
			fseek($bc, $ofs, SEEK_SET);
			$block = fread($bc, $len);
			$hash = hash((new \App\BlockchainLite\Blockchain())->hashalg, $block);
			// add new block to the end of the chain
			fseek($bc, 0, SEEK_END);
			$pos = ftell($bc);
			\App\BlockchainLite\Blockchain::write_block($bc, $data, $hash);
			fclose($bc);
			// update index
			\App\BlockchainLite\Blockchain::update_index($ix, $pos, strlen($data), ($maxblock+1));
			fclose($ix);
			return TRUE;
		}
		else
		{
			if (file_exists($fn)) return('Blockchain data file already exists!');
			if (file_exists($indexfn)) return('Blockchain index file already exists!');
			$bc = fopen($fn, 'wb');
			$ix = fopen($indexfn, 'wb');
			\App\BlockchainLite\Blockchain::write_block($bc, $data, str_repeat('00', (new \App\BlockchainLite\Blockchain())->hashlen));
			\App\BlockchainLite\Blockchain::update_index($ix, 0, strlen($data), 1);
			fclose($bc);
			fclose($ix);
			return TRUE;
		}
		echo "end-test";
	}

	public static function write_block(&$fp, $data, $prevhash) {
		fwrite($fp, pack('V', (new \App\BlockchainLite\Blockchain())->magic), 4);                // Magic
		fwrite($fp, chr(1), 1);                           // Version
		fwrite($fp, pack('V', time()), 4);                // Timestamp
		fwrite($fp, hex2bin($prevhash), (new \App\BlockchainLite\Blockchain())->hashlen);        // Previous Hash
		fwrite($fp, pack('V', strlen($data)), 4);         // Data Length
		fwrite($fp, $data, strlen($data));                // Data
	}

	public static function update_index(&$fp, $pos, $datalen, $count) {
		fseek($fp, 0, SEEK_SET);
		fwrite($fp, pack('V', $count), 4);                // Record count
		fseek($fp, 0, SEEK_END);
		fwrite($fp, pack('V', $pos), 4);                  // Offset
		fwrite($fp, pack('V', ($datalen + \App\BlockchainLite\Blockchain::blksize())), 4);		// Length
	}

	public function WalkChain($uid) // Consistency check
	{
		$fn = "/var/www/blockchain/user_".$uid.".dat";
		$hashlen = 0;
		$hashalg = "sha256";
		
		if (!file_exists($fn)) exit("Can't open ".$fn);
		$size = filesize($fn);
		$fp = fopen($fn,'rb');

		$height = 0;
		$pHash = "";
		$invalidChain = false;
		while (ftell($fp) < $size) {

			$header = fread($fp, (13+(new \App\BlockchainLite\Blockchain())->hashlen));

			$magic = \App\BlockchainLite\Blockchain::unpack32($header,0);
			$version = ord($header[4]);
			$timestamp = \App\BlockchainLite\Blockchain::unpack32($header,5);
			$prevhash = bin2hex(substr($header,9,(new \App\BlockchainLite\Blockchain())->hashlen));
			$datalen = \App\BlockchainLite\Blockchain::unpack32($header,-4);
			$data = fread($fp, $datalen);
			$hash = hash((new \App\BlockchainLite\Blockchain())->hashalg, $header.$data);
			
			/*
			*
			* Consistency check
			*
			*/
			if($pHash ==""){
				$pHash = $prevhash;
			}else{
				if($pHash != $prevhash)
				{
					// Invalid link
					$invalidChain = true;
					break;
				}else{
					// Valid Link
				}
			}

			$pHash = $hash;
			/*
			*
			* End Consistency check
			*
			*/

			/*
			*
			* Print Block Data
			*
			*/
			
			
			/*
			print "height...... ".++$height."<br />";
			print "magic....... ".dechex($magic)."<br />";
			print "version..... ".$version."<br />";
			print "timestamp... ".$timestamp." (".date("H:i:s m/d/Y",$timestamp).")<br />";
			print "prevhash.... ".$prevhash."<br />";
			print "blockhash... ".$hash."<br />";
			print "datalen..... ".$datalen."<br />";
			print "data........ ".wordwrap($data, 100)."<br /><br />";
			*/
		}

		fclose($fp);

		return !$invalidChain;
	}




	public static function unpack32($data,$ofs) {
		return unpack('V', substr($data,$ofs,4))[1];
	}
}
