var app = angular.module("btcApps", []).config(function($interpolateProvider){$interpolateProvider.startSymbol('{[{').endSymbol('}]}');});
app.controller("btcappsController", function($scope) {

	$scope.walletAddresses = [];

	$scope.eth_walletAddresses = [];
	$scope.lock = false;


    $scope.disableInput = (function(){
    	$(".generate-waddress-button").prop('disabled', true);
    	$(".delete-waddress-button").prop('disabled', true);
    	$(".generate-waddress-button").stop(1);
    	$(".generate-waddress-button").fadeOut(1000, function(){
    		$(".generate-waddress-button").prop('disabled', false);
    		$(".delete-waddress-button").prop('disabled', false);
    	});
    })

    $scope.enableInput = (function(){
		$(".generate-waddress-button").prop('disabled', false);
		$(".delete-waddress-button").prop('disabled', false);
		$(".generate-waddress-button").fadeIn(1000);
    })


    generateWalletAddress = (function(){
    	$scope.disableInput();
    	$.get("/exchange/public/index.php/Bitcoind/Wallet/GenerateAddress", function(data){
			grabMyAddresses();
    		$scope.enableInput();
		});
    });

    eth_generateWalletAddress = (function(){
    	$scope.disableInput();
    	$.get("/exchange/public/index.php/Ethereumd/Wallet/GenerateAddress", function(data){
			grabMyAddresses();
    		$scope.enableInput();
		});
    });

    $scope.deleteWalletAddress = (function(address){
    	$scope.disableInput();
    	$.get("/exchange/public/index.php/Bitcoind/Wallet/"+address+"/Delete", function(data){
			grabMyAddresses();
		});
    });

    $scope.eth_deleteWalletAddress = (function(address){
    	$scope.disableInput();
    	$.get("/exchange/public/index.php/Ethereumd/Wallet/"+address+"/Delete", function(data){
			grabMyAddresses();
		});
    });

    grabMyAddresses = (function(){
    	$scope.disableInput();
    	$.get("/exchange/public/index.php/Bitcoind/Wallet/MyAddresses", function(data){

			$scope.walletAddresses = data;
			$scope.$apply();
		});

    	$.get("/exchange/public/index.php/Ethereumd/Wallet/MyAddresses", function(data){

			$scope.eth_walletAddresses = data;
			$scope.$apply();
		});

		$scope.enableInput();

    });

    grabMyAddresses();

});