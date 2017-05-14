var app = angular.module("btcApps", []).config(function($interpolateProvider){$interpolateProvider.startSymbol('{[{').endSymbol('}]}');});
app.controller("btcappsController", function($scope) {

	$scope.walletAddresses = [];

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

    $scope.deleteWalletAddress = (function(address){
    	$scope.disableInput();
    	$.get("/exchange/public/index.php/Bitcoind/Wallet/"+address+"/Delete", function(data){
			grabMyAddresses();
    		$scope.enableInput();
		});
    });

    grabMyAddresses = (function(){
    	$scope.disableInput();
    	$.get("/exchange/public/index.php/Bitcoind/Wallet/MyAddresses", function(data){

			$scope.walletAddresses = data;
    		$scope.enableInput();
			$scope.$apply();
		});
    });

    grabMyAddresses();

});