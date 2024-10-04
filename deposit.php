<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];

$amtToDeposit = 0;
$showForm1 = true;
$formMsg = "";
if(isset($_POST['form1btn'])){
	$amtToDeposit = trim(mysqli_real_escape_string($conn,$_POST['tokenamt']));
	
	if($amtToDeposit == ""){
		$formMsg = "All fields are required!";
	}else if(!is_numeric($amtToDeposit){
		$formMsg = "Value must be numeric!";
	}else{
		$showForm1 = false;
	}
}
if(isset($_POST['trxid'])){
	$trxID = trim(mysqli_real_escape_string($conn,$_POST['trxid']));
	$tokenAmt = trim(mysqli_real_escape_string($conn,$_POST['tokenamt']));
	$errMessage = trim(mysqli_real_escape_string($conn,$_POST['errmsg']));
	if($trxID != "-"){
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
	}else{
		$formMsg = $errMessage;
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
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  
  <!-- javascript functions -->
  <script src="web3libs/solanaweb3.js"></script>
  <script src="web3libs/Base58.min.js"></script>
  <script>
	function getEndPoint(epUrl)
	{
		let url = web3.clusterApiUrl(epUrl);
		return url;
	}

	async function transferFromUser(dappAddr, userAddr, amount, endpt)
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
				var connection = new solanaWeb3.Connection(getEndPoint(endpt),"confirmed");
				var dappWallet = new solanaWeb3.Publickey(dappAddr);
				let transaction = new solanaWeb3.Transaction().add(solanaWeb3.SystemProgram.transfer({fromPubkey: userWallet, toPubkey: dappWallet, lamports: amount}));
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
  
	async function tokenDeposit(dAddr,uAddr,depAmt,endpt)
	{
		const theForm = document.getElementById("depform");
		theForm.addEventListener("submit",(e) => {e.preventDefault();});
		const result = await transferFromUser(dAddr, uAddr, depAmt, endpt);
		theForm.trxid.value = result.trx_id;
		theForm.errmsg.value = result.trx_status;
		theForm.submit();
	}
  </script>

</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="index.html" class="logo d-flex align-items-center">
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
					  <h5 class="card-title"><?php echo $formMsg; ?></h5>
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
				<div class="col-lg-6">
				  <div class="card info-card customers-card">
					<div class="card-body">
						<h5 class="card-title">Wallet <span>| Deposit</span></h5>
						<div class="d-flex align-items-center">
							<div class="ps-3">
								<div class="text-center"><br></div>
								<form method="post" action="deposit.php">
									<div class="row mb-3">
									  <label for="input2" class="col-sm-2 col-form-label">Amount to Deposit (In SOL)</label>
									  <div class="col-sm-10">
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
			
			<div class="col-lg-6">
			  <div class="card info-card customers-card">
				<div class="card-body">
					<h5 class="card-title">Wallet <span>| Confirm Deposit</span></h5>
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