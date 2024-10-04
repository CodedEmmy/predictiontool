//This file is no longer used due to module scope complications. Left here for future reference
//imports
import nacl from "./nacl-fast.js";
import Base58 from "./Base58.min.js";
import * as web3 from "./solanaweb3.js";

function phantomLogin()
{
	let walletAddr = "None";
	let errMsg = "-";
	const isPhantomAvailable = window.solana && window.solana.isPhantom;
	if(isPhantomAvailable){
		try{
			const resp = await window.solana.connect();
			walletAddr = resp.publicKey.toString();
			errMsg = "Connected";
		}catch(err){
			errMsg = "User rejected Request";
		}
	}else{
		errMsg = "Phantom wallet is not detected";
	}
	return {walletAddress:walletAddr, errMessage:errMsg};
}

function signInWallet(walletAddr, theMsg)
{
	const encMessage = new TextEncoder().encode(theMsg);
	const signedMessage = await window.solana.signMessage(walletAddr, encMessage);
	//const signedMessage = await window.solana.signMessage(encMessage, "utf8");
	const verified = nacl.sign.detached.verify(encMessage, signedMessage.signature, signedMessage.publicKey.toBytes());
	if(walletAddr != signMessage.publicKey){
		verified = false;
	}
	return verified;
}

function getEndPoint(epUrl)
{
	let url = web3.clusterApiUrl(epUrl);
	return url;
}

function transferToUser(userAddr, amount, privkey, endpt)
{
	const connection = new web3.Connection(getEndPoint());
	const privateKey = new Uint8Array(Base58.decode(privkey));
	const dappAccount = web3.Keypair.fromSecretKey(privateKey);
	const userWallet = new web3.Publickey(userAddr);
	var signTrx = "-";
	var fnErr = "-";
	try{
		(async() =>{
			const transaction = new web3.Transaction();
			transaction.add(web3.SystemProgram.transfer({fromPubkey: dappAccount.publicKey, toPubkey: UserWallet, lamports: amount}));
			signature = await web3.sendAndConfirmTransaction(connection, transaction,[dappAccount],);
			signTrx = signature.signature;
			fnErr = "Confirmed";
		})();
	}catch(err){
		signTrx = "-";
		fnErr = err;
	}
	return {trxID: signTrx, errMessage: fnErr};
}

function transferFromUser(dappAddr, userAddr, amount, endpt)
{
	let txid = "-";
	let fnMsg = "-";
	const phantom = window.solana;
	if(!phantom){
		fnMsg = "Phantom Wallet not detected";
	}else{
		const userWallet = await phantom.publicKey;
		if(userAddr != userWallet.publicKey){
			fnMsg = "Wallet mismatch: Ensure that you are connected to " + userAddr;
		}else{
			var connection = new web3.Connection(getEndPoint(endpt),"confirmed");
			var dappWallet = new web3.Publickey(dappAddr);
			let transaction = new web3.Transaction().add(web3.SystemProgram.transfer({fromPubkey: userWallet, toPubkey: dappWallet, lamports: amount}));
			transaction.feePayer = userWallet;
			let blockhashObj = await connection.getRecentBlockhash();
			transaction.recentBlockhash = blockhashObj.blockhash;
			try{
				let signature = await phantom.signAndSendTransaction(transaction);
				await connection.confirmTransaction(signature.signature);
				txid = signature.signature;
			}catch(err){
				fnMsg = "Error: " + err;
			}
		}
	}
	return {trx_id: txid, trx_status: fnMsg};
}