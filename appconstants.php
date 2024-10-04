<?php
//Define DApp-wide constants

$LAMPS_PER_SOL = 1000000000;

//For storing DApp Funds
//$DAPP_WALLET_ADDRESS = "Your dApp Solana Account Wallet Address";
$DAPP_WALLET_ADDRESS = "F2bufEar5aFfs7bEkE1JMT5yUmqgjRYUREj9rrtg3yBN";

//For signing outgoing transactions
//$DAPP_PRIVATE_KEY = "Your dApp wallet private key";
$DAPP_PRIVATE_KEY = "3W23mbmGL2E3eiboCCwK93wyfjMzKVMY4oNZXukrTUGLa9r8AgMohvyndRhXGqkGaPSp1m1mvXPtogmNAiFU7Xhr";

$RPC_ENDPOINT = "devnet"; //"mainnet-beta", or "Your preferred end point url string here"

$MIN_DEPOSIT = 0.01 * $LAMPS_PER_SOL;
$MIN_WITHDRAW = 0.007 * $LAMPS_PER_SOL;
$MIN_INCENTIVE = 0.05 * $LAMPS_PER_SOL;
$POLL_FEE = 0.01 * $LAMPS_PER_SOL;
?>