<h5 class='col-md-12'>Balances</h5>
<table class="table table-bordered">
    <thead>
    <tr>
        <th>Currency</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>
        <tr>
            <td>Ƀitcoin</td>
            <td><small>Ƀ</small> 0.00<span style='color: #ccc'>000</span></td>
        </tr>
        <tr>
            <td>United States Dollar</td>
            <td><small>$</small> {{ $usdfunds != null ? $usdfunds->funds_remaining : 0 }}</td>
        </tr>
    </tbody>
</table>