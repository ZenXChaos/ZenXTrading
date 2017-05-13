# ZenXTrading
A community governed crypto-currency exchange software.


## What is the ZenX Trading Platform?

This software is being developed to accomplish several things. Foremost though, it is an open-source digital assets trading platform (currently in development).

## Community goverened exchange

 The community should have more authority on how an exchange that they put their money into should be ran. From associated fees to exchange limits, we finally take this power from corporations -- and puts it back into the community. (planned) Using an Ethereum based voting solution the community will drive the majority of functioning.

## Exchange digital assets

While currently only USD and BTC are supported, other coins are in the works. More specifically Litecoin, Ethereum, and Ripple.

## Trading on binary options

Trade digital assets based on binary options.

# Dividends pool

This is perhaps the most attractive feature planned for this project. To combat fee manipulation, there are hopes to implement a dividends pool which will encourage users to pay higher fees. Dividends will be distrubted amongst a top X % of traders (not based on profit/loss). While associated fees will still be lower than industry price, those fees (mostly) will be contributed to the pool so that it is reinvested into the users which generated income. Instead of fees going to corporations, we should be earning dividends on digital assets invested. 

# What is implemented ?

* User registration / login

* USD Funding via Credit Card (uses Paypal SDK)

* Generate BTC Wallets and Addresses. (Currently not saved to profile, but works)

* Submit USD<->BTC orders (Orders currently not executed though)

* Basic data integrity using Blockchain style comparison

* Laravel 5

* Demo: http://192.169.148.246/exchange/public/index.php/home (Only those with this link will find it. Design is horrible.)

See a full list of features, plans, and issues [here](https://github.com/ZenXChaos/ZenXTrading/projects/1)!


----

# Configuration and Dependencies

## MySQL Database

`~/exchange/config/database.php` <- Set your MySQL Database information here

Import the SQL script in the master-dev root folder.

## BlockCypher

This project relies on the BlockCypher SDK to generate Bitcoin addresses and wallets. Visit https://blockcypher.com/ to create a key. Download the SDK [here](https://github.com/blockcypher).

Set your token in `~/exchange/app/Http/Controllers/BitcoindController.php`.

## PayPal SDK

This project relies on the PayPal-SDK to process payments. Visit https://developer.paypal.com/ to create an app.

Set your app credentials in `~/exchange/app/Http/Controllers/PaymentGateway/PaypalController.php`.

## App Structure

To ensure your SDK's are loaded, please use the following directory format unless otherwise specified in `~/exchange/public/index.php`.

---- exchange

---- app

---- public

---- resources

---- PayPal-SDK

---- BlockCypher