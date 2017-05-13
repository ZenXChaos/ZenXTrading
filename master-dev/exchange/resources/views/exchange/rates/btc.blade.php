<h5 class='col-md-12'>Sells</h5>
<table class="table table-bordered">
	<thead>
	<tr>
		<th>Seller</th>
		<th>Ƀitcoin</th>
		<th>Bid</th>
	</tr>
	</thead>
	<tbody>
		@foreach($sell_orders as $sell_share)
		<tr>
			<td>{{ $sell_share->owner->username }}</td>
			<td><small>Ƀ</small> {{ $sell_share->request_amount - $sell_share->filled }}</td>
			<td><small>$</small> {{ $sell_share->bid }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<br /><br />
<h5 class='col-md-12'>Buys</h5>
<table class="table table-bordered">
	<thead>
	<tr>
		<th>Buyer</th>
		<th>Ƀitcoin</th>
		<th>Bid</th>
	</tr>
	</thead>
	<tbody>
		@foreach($buy_orders as $buy_share)
		<tr>
			<td>{{ $buy_share->owner->username }}</td>
			<td><small>Ƀ</small> {{ $buy_share->request_amount - $buy_share->filled }}</td>
			<td><small>$</small> {{ $buy_share->bid }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
<br />
<i>Please be aware that orders are filled as soon as a match is available -- regardless of current market price. You will never pay more than what you have requested but could gain more.</i>