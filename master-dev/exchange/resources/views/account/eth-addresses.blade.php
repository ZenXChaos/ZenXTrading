<button class="generate-waddress-button button button-primary" onclick="eth_generateWalletAddress()">Generate address</button><br /><br />
<table class="table table-bordered" ng-controller="btcappsController">
    <thead>
    <tr>
        <th>Address</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>
        <tr ng-repeat="eth_walletAddress in eth_walletAddresses">
            <td>{[{ eth_walletAddress.wallet_address }]} <button class="button button-danger delete-waddress-button" ng-click="eth_deleteWalletAddress(eth_walletAddress.wallet_address)">Delete</button></td>
            <td><small>ETH</small> {[{ eth_walletAddress.final_balance }]}<small></small>  </td>
        </tr>
    </tbody>
</table>