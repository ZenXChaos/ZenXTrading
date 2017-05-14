<button class="generate-waddress-button button button-primary" onclick="generateWalletAddress()">Generate address</button><br /><br />
<table class="table table-bordered" ng-controller="btcappsController">
    <thead>
    <tr>
        <th>Address</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>
        <tr ng-repeat="walletAddress in walletAddresses">
            <td>{[{ walletAddress.wallet_address }]} <button class="button button-danger delete-waddress-button" ng-click="deleteWalletAddress(walletAddress.wallet_address)">Delete</button></td>
            <td><small>Ƀ</small>{[{ walletAddress.final_balance }]}<small></small>  </td>
        </tr>
    </tbody>
</table>