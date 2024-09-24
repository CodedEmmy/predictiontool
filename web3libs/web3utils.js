//imports
import nacl from './nacl-fast.js';
import * from './solanaweb3.js';

async function phantomLogin()
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
	return verified;
}