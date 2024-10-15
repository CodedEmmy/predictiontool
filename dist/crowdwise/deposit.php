<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
require_once("appconstants.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];

$amtToDeposit = 0;
$showForm1 = true;
$formMsg = "";
if(isset($_POST['form1btn'])){
	$amtToDeposit = trim(mysqli_real_escape_string($conn,$_POST['tokenamt']));
	$minDeposit = $MIN_DEPOSIT/$LAMPS_PER_SOL;
	if($amtToDeposit == ""){
		$formMsg = "All fields are required!";
	}else if(!is_numeric($amtToDeposit)){
		$formMsg = "Deposit amount must be a numeric value!";
	}else if($amtToDeposit < $minDeposit){
		$formMsg = "Deposit amount must be greater or equal to $minDeposit SOL!";
	}else{
		$showForm1 = false;
	}
}
if(isset($_POST['trxid'])){
	$trxID = trim(mysqli_real_escape_string($conn,$_POST['trxid']));
	$tokenAmt = trim(mysqli_real_escape_string($conn,$_POST['tokenamt']));
	$errMessage = trim(mysqli_real_escape_string($conn,$_POST['errmsg']));
	$trxStatus = trim(mysqli_real_escape_string($conn,$_POST['txstatus']));
	$formMsg = $errMessage;
	if($trxID != "-"){
		if($trxStatus == "True"){
			$q = "select current_amt from user_accounts where user_id = '$userID'";
			$res = mysqli_query($conn, $q);
			$pdata2 = mysqli_fetch_assoc($res);
			$newAmount = $pdata2['current_amt'] + $tokenAmt;
			$q = "update user_accounts set current_amt = $newAmount where user_id = '$userID'";
			mysqli_query($conn, $q);
			$today = date("Y-m-d H:i:s");
			$q = "insert into wallet_history (user_id, reward_amt, poll_id, activity_date, activity_type, activity_desc, trx_id) values('$userID', '$tokenAmt', '0', '$today', 'IN', 'User Deposit', '$trxID')";
			mysqli_query($conn, $q);
			header("location: wallet.php");
		}
	}
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>CrowdWise Prediction Tool</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  
  <!-- javascript functions -->
  <!-- <script src="web3libs/solanaweb3.js"></script> -->
  <!-- fall back to version 1.30.2 to avoid buffer error -->
  <script src="web3libs/solanaweb3v1_30_2.js"></script>
  <script src="web3libs/Base58.min.js"></script>
  <script>
	async function sleep(ms)
	{
		return new Promise(resolve => setTimeout(resolve, ms));
	}
	
	async function isBlockhashExpired(connection: Connection, lastBlockHeight: number)
	{
		let currentBlockHeight = (await connection.getBlockHeight('finalized'));
		return (currentBlockHeight > lastBlockHeight - 150);
	}
	
	async function transferFromUser(dappAddr, userAddr, amount, endpt)
	{	
		let txSign = "-";
		let fnMsg = "-";
		let txStatus = "-";
		const phantom = window.solana;
		if(!phantom){
			fnMsg = "Phantom Wallet not detected";
		}else{
			let userWallet = "";
			let doTrx = false;
			try{
				const resp = await phantom.connect();
				userWallet = resp.publicKey;
				doTrx = true;
			}catch(err){
				fnMsg = "User rejected the request";
				doTrx = false;
			}
			if(doTrx){
				if(userAddr != userWallet.toString()){
					fnMsg = "Wallet mismatch: Ensure that you are connected to " + userAddr;
				}else{
					var connection = new solanaWeb3.Connection(endpt, "confirmed",);
					var dappWallet = new solanaWeb3.PublicKey(dappAddr);
					let transaction = new solanaWeb3.Transaction();
					transaction.add(solanaWeb3.SystemProgram.transfer({fromPubkey: userWallet, toPubkey: dappWallet, lamports: amount,}),);
					transaction.feePayer = userWallet;
						
					var	blockhashObj = await connection.getRecentBlockhash();
						//blockhashObj = await connection.getLatestBlockhash();
					transaction.recentBlockhash = blockhashObj.blockhash;
						
					let signature = await phantom.signAndSendTransaction(transaction);
					txSign = signature.signature;
					
					var hashExpired = false;
					var txSuccess = false;
					var checkStatus = true;
					while(checkStatus){
						var status = await connection.getSignatureStatus(txSign, {searchTransactionHistory:true,});
						console.log(status);
						if(status.value === null){
							txStatus = "False";
							fnMsg = "Failed to get transaction status";
							checkStatus = false;
						}else{
							if(status.value.err !== null){
								//trx Failed
								txStatus = "False";
								fnMsg = "Transaction failed: ${JSON.stringify(status.value.err)}";
								checkStatus = false;
							}else{
								if(status.value.confirmationStatus === 'confirmed' || status.value.confirmationStatus === 'finalized'){
									txSuccess = true;
									checkStatus = false;
									txStatus = "True";
									fnMsg = "Deposit Successful";
								}						
							}
						}
						if(!txSuccess){
							hashExpired = await isBlockhashExpired(connection, lastValidHeight);
							if(hashExpired){
								txStatus = "False";
								checkStatus = false;
								fnMsg = "Transaction expired: You may retry.";
							}else{
								//wait and check status again in 2.5 seconds
								await sleep(2500);
							}
						}
					}
					
				}
			}
		}
		return {trxID: txSign, errMessage: fnMsg, trxStatus: txStatus};
	}
  
	async function tokenDeposit(dAddr,uAddr,depAmt,endpt)
	{
		const theForm = document.getElementById("depform");
		theForm.addEventListener("submit",(e) => {e.preventDefault();});
		const result = await transferFromUser(dAddr, uAddr, depAmt, endpt);
		theForm.trxid.value = result.trxID;
		theForm.errmsg.value = result.errMessage;
		theForm.txstatus.value = result.trxStatus;
		theForm.submit();
	}
  </script>

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.php" class="logo d-flex align-items-center">
        <img src="assets/img/logo.png" alt="">
        <span class="d-none d-lg-block">CrowdWise</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->

        <li class="nav-item dropdown pe-3">
          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="assets/img/profileicon.png" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo $userName; ?></span>
          </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li class="dropdown-header">
              <h6><?php echo $userName; ?></h6>
              <span><?php echo $walletAddress; ?></span>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="settings.php">
                <i class="bi bi-gear"></i>
                <span>Account Settings</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="about.php">
                <i class="bi bi-question-circle"></i>
                <span>About</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="logout.php">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->

  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">

    <ul class="sidebar-nav" id="sidebar-nav">

      <li class="nav-item">
        <a class="nav-link collapsed" href="home.php">
          <i class="bi bi-grid"></i>
          <span>Dashboard</span>
        </a>
      </li><!-- End Dashboard Nav -->
	  
	  <li class="nav-item">
        <a class="nav-link collapsed" href="wallet.php">
          <i class="bi bi-wallet2"></i>
          <span>Wallet</span>
        </a>
      </li>
	  
	  <li class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#forms-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-journal-text"></i><span>Polls</span><i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="forms-nav" class="nav-content collapse " data-bs-parent="#sidebar-nav">
          <li>
            <a href="newpoll.php">
              <i class="bi bi-circle"></i><span>Create Poll</span>
            </a>
          </li>
          <li>
            <a href="mypolls.php">
              <i class="bi bi-circle"></i><span>Your Polls</span>
            </a>
          </li>
          <li>
            <a href="votedpolls.php">
              <i class="bi bi-circle"></i><span>Voted Polls</span>
            </a>
          </li>
        </ul>
      </li><!-- End Polls Nav -->

      <li class="nav-item">
        <a class="nav-link collapsed" href="poll_list.php">
          <i class="bi bi-list-ol"></i>
          <span>Vote</span>
        </a>
      </li>
      <li class="nav-item">
        <a class="nav-link collapsed" href="settings.php">
          <i class="bi bi-gear"></i>
          <span>Account Settings</span>
        </a>
      </li>

      <li class="nav-item">
        <a class="nav-link collapsed" href="logout.php">
          <i class="bi bi-box-arrow-in-right"></i>
          <span>Logout</span>
        </a>
      </li>

    </ul>

  </aside><!-- End Sidebar-->

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>User Wallet</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item active">Deposit</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
	<section class="section">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body">
					  <h5 class="card-title"><span style="color:#600"><?php echo $formMsg; ?></span></h5>
					  <?php $dFee = $MIN_DEPOSIT/$LAMPS_PER_SOL; ?>
					  <p>Minimum Deposit Amount: <?php echo $dFee; ?> SOL</p>
					</div>
				</div>
			</div>
		</div>
	</section>
	<?php
	if($showForm1){
	?>
		<section class="section">
			<div class="row">
				<div class="col-lg-12">
				  <div class="card info-card customers-card">
					<div class="card-body">
						<h5 class="card-title">Wallet <span>| Deposit</span></h5>
						<div class="d-flex align-items-center">
							<div class="ps-3">
								<div class="text-center"><br></div>
								<form method="post" action="deposit.php">
									<div class="row mb-3">
									  <label for="input2" class="col-sm-6 col-form-label">Amount to Deposit (In SOL)</label>
									  <div class="col-sm-6">
										<input type="text" class="form-control" id="input2" name="tokenamt" value="<?php echo $amtToDeposit; ?>">
									  </div>
									</div>
									<div class="text-center">
									  <button type="submit" class="btn btn-primary" name="form1btn">Next</button>
									</div>
								</form>
							</div>
						</div>
					</div>
				  </div>
				</div>
			</div>
		</section>
	<?php
	}else{
	?>
		<section class="section">
		  <div class="row">
			
			<div class="col-lg-12">
			  <div class="card info-card customers-card">
				<div class="card-body">
					<h5 class="card-title">Wallet <span> | Confirm Deposit</span></h5>
					<div class="d-flex align-items-center">
						<div class="ps-3">
							<form method="post" action="deposit.php" id="depform">
								<?php
								$amtInLamp = $amtToDeposit * $LAMPS_PER_SOL;
								$fnParams = "'$DAPP_WALLET_ADDRESS', '$walletAddress','$amtInLamp','$RPC_ENDPOINT'";
								?>
								<input type="hidden" name="tokenamt" value="<?php echo $amtInLamp; ?>">
								<input type="hidden" name="trxid" value="-">
								<input type="hidden" name="errmsg" value="-">
								<input type="hidden" name="txstatus" value="-">
								
								<div class="row mb-3">
								  <label for="input2" class="col-sm-6 col-form-label">Amount to Deposit (In SOL)</label>
								  <div class="col-sm-6">
									<input type="text" class="form-control" value="<?php echo $amtToDeposit; ?>" disabled>
								  </div>
								</div>
								
								<div class="text-center">
									<button onclick="tokenDeposit(<?php echo $fnParams; ?>)" class="btn btn-primary" name="formbtn">Deposit</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			  </div>
			</div>
			
		  </div>
		</section>
		<?php
	}
	?>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>
</html>