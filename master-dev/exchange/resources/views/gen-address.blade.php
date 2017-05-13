@extends('app')

@section('content')
<script>
    
</script>

<div class="container">
        <div class="row">
                <div class="col-md-10 col-md-offset-1">
                        <div class="panel panel-default">
                                <div class="panel-heading">Create new address!</div>

                                <div class="panel-body">
                                        Your <i>new</i>  Ƀitcoin wallet address is: {{ $address }} : ฿ 0.00<span style='color: #ccc'>000</span>

                                        <br />
                                        <button class="privkey-selector">Show Private Key</button>
                                        <br />
                                        <small style="background: #ccc;">
                                            <i class="privkey-message">
                                                After displaying your private key, it will destruct and become inaccessible to anyone other than yourself. <b>We will not createa a backup</b>!
                                            </i>
                                        </small>

                                        <div class='privkey' style="display: none;">
                                            Public Key: {{ $publickey }} <br />
                                            Private key: {{ $privatekey }} <br />
                                            Private Key (WIF): {{ $privatekey_wif }}<br />

                                        </div>
                                </div>
                        </div>
                </div>
        </div>
</div>
@endsection