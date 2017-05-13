@extends('app')
@section('content')
	<style>
		.deposit-usd{
			background: white;
			border: 1px solid black;
			height: 400px;
			margin: auto;
			padding: 7px;
			/*display: none;*/
			width: 750px;
			z-index: 999;

		}

		.panel-heading{
			border: 0px;
			padding: 0px;
			padding-left: 3px;
			padding-top: 3px;
		}
	</style>

	<div class="container">
		<div class="row">
			<div class="col-md-10 col-md-offset-1">
				<div class="panel panel-default">
					<div class="panel-heading">
						<ul class="nav nav-tabs">
							<li class="active"><a data-toggle="tab" href="#home">Overview</a></li>
							<li><a data-toggle="tab" href="#menu1">Trade</a></li>
							<li><a data-toggle="tab" href="#menu2">Funding</a></li>
							<li><a data-toggle="tab" href="#menu3">Security</a></li>
						</ul>
					</div>
					<div class="panel-body">
						<div class="tab-content">
							<div id="home" class="tab-pane fade in active">
								<h3>HOME</h3>
								<div class='balances-content col-md-6'>
									@include('account.balances')
								</div>
								<div class='balances-content col-md-6'>
									@include('exchange.rates.btc')
								</div>
							</div>
							<div id="menu1" class="tab-pane fade">
								<div class='trade-view'>
									
									@include('exchange.onboard')
								</div>
							</div>
							<div id="menu2" class="tab-pane fade">
								@include('account.linked-payments')
							</div>
							<div id="menu3" class="tab-pane fade">
								<h3>Menu 3</h3>
								<p>Some content in menu 3.</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<script>
		$(document).ready(function(){

			$(".submit-payment").click(function(){
				if(parseInt($("input[name='USD']").val()) < 1 || $("input[name='USD']").val() < 1 )
				{
					alert("Must enter value greater than 0.99");
					return;
				}else{
					$.get("/exchange/public/index.php/Funding/Paypal/PurchaseCurrency", {USD: $("input[name='USD']").val()}, function(){
						window.location = '/exchange/public/index.php/home';
					});
					$(".deposit-usd").html("<center>Please wait... Processing...</center>");
					
				}
			});
		});
	</script>
@endsection