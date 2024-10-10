<?php
//Define DApp-wide constants

$LAMPS_PER_SOL = 1000000000;

//For storing DApp Funds
$DAPP_WALLET_ADDRESS = "Your dApp Solana Account Wallet Address";

//For signing outgoing transactions
$DAPP_PRIVATE_KEY = "Your dApp wallet private key";

//$RPC_ENDPOINT = "devnet"; //"mainnet-beta", or "Your preferred end point url string here"
$RPC_ENDPOINT = "https://rpc.ankr.com/solana_devnet";

$MIN_DEPOSIT = 0.01 * $LAMPS_PER_SOL;
$MIN_WITHDRAW = 0.007 * $LAMPS_PER_SOL;
$MIN_INCENTIVE = 0.05 * $LAMPS_PER_SOL;
$POLL_FEE = 0.003 * $LAMPS_PER_SOL;
?>
