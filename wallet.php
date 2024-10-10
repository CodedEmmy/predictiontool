<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
require_once("appconstants.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];

$formMsg = "";
if(isset($_POST['tokenamt'])){
	$trxID = trim(mysqli_real_escape_string($conn,$_POST['trxid']));
	$tokenAmt = trim(mysqli_real_escape_string($conn,$_POST['tokenamt']));
	$errMessage = trim(mysqli_real_escape_string($conn,$_POST['errmsg']));
	if($trxID != "-"){
		$q = "select withdrawn_amt from user_accounts where user_id = '$userID'";
		$res = mysqli_query($conn, $q);
		$pdata2 = mysqli_fetch_assoc($res);
		$newWithdrawn = $pdata2['withdrawn_amt'] + $tokenAmt;
		$q = "update user_accounts set current_amt = 0, withdrawn_amt = $newWithdrawn where user_id = '$userID'";
		mysqli_query($conn, $q);
		$today = date("Y-m-d H:i:s");
		$q = "insert into wallet_history (user_id, reward_amt, poll_id, activity_date, activity_type, activity_desc, trx_id) values('$userID', '$tokenAmt', '0', '$today', 'OUT', 'Reward Withdrawal', '$trxID')";
		mysqli_query($conn, $q);
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
  
  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">
  <!-- javascript functions  -->
  <script src="web3libs/solanaweb3.js"></script>
  <script src="web3libs/Base58.min.js"></script>
  <script>
	async function transferToUser(userAddr, amount, privkey, endpt)
	{
		//const connection = new solanaWeb3.Connection(solanaWeb3.clusterApiUrl(endpt),"confirmed");
		var connection = new solanaWeb3.Connection(endpt, "confirmed",);
		const privateKey = new Uint8Array(Base58.decode(privkey));
		const dappAccount = solanaWeb3.Keypair.fromSecretKey(privateKey);
		const userWallet = new solanaWeb3.Publickey(userAddr);
		var signTrx = "-";
		var fnErr = "-";
		try{
			(async() =>{
				const transaction = new solanaWeb3.Transaction();
				transaction.add(solanaWeb3.SystemProgram.transfer({fromPubkey: dappAccount.publicKey, toPubkey: UserWallet, lamports: amount}));
				signature = await solanaWeb3.sendAndConfirmTransaction(connection, transaction,[dappAccount],);
				signTrx = signature.signature;
				fnErr = "Confirmed";
			})();
		}catch(err){
			signTrx = "-";
			fnErr = err;
		}
		return {trxID: signTrx, errMessage: fnErr};
	}
	
	async function rewardClaim(waddr,claimAmt,pkey,endpt)
	{
		const theForm = document.getElementById("claimform");
		theForm.addEventListener("submit",(e) => {e.preventDefault();});
		const result = await transferToUser(waddr, claimAmt, pkey, endpt);
		theForm.trxid.value = result.trxID;
		theForm.errmsg.value = result.errMessage;
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
          <li class="breadcrumb-item active">Wallet</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <?php
		$sql = "select current_amt, withdrawn_amt from user_accounts where user_id = '$userID'";
		$res = mysqli_query($conn, $sql);
		$pdata = mysqli_fetch_assoc($res);
		?>
		<div class="col-lg-5">
          <div class="card info-card customers-card">
            <div class="card-body">
				<h5 class="card-title">Balance <span>| Available</span></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="ps-3">
						<?php
						$amtInSol = $pdata['current_amt']/$LAMPS_PER_SOL;
						echo "<h5>$amtInSol SOL</h5>";
						$minAmt = $MIN_WITHDRAW/$LAMPS_PER_SOL;
						?>
						<span class="text-danger small pt-1 fw-bold"><?php echo "Minimum Withdrawal : $minAmt SOL"; ?></span>
                    </div>
                </div>
            </div>
          </div>
        </div>
		<div class="col-lg-4">
          <div class="card info-card customers-card">
            <div class="card-body">
				<h5 class="card-title">Balance <span>| Withdrawn</span></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div class="ps-3">
						<?php
						$amtInSol = $pdata['withdrawn_amt']/$LAMPS_PER_SOL;
						echo "<h5>$amtInSol SOL</h5>";
						?>
                    </div>
                </div>
            </div>
          </div>
        </div>
		
		<div class="col-lg-3">
          <div class="card info-card customers-card">
            <div class="card-body">
				<h5 class="card-title">Wallet <span>| In/Out</span></h5>
                <div class="d-flex align-items-center">
                    <div class="ps-3">
						<form method="post" action="wallet.php" id="claimform">
							<input type="hidden" name="tokenamt" value="<?php echo $pdata['current_amt']; ?>">
							<input type="hidden" name="trxid" value="-">
							<input type="hidden" name="errmsg" value="-">
							<div class="text-center">
								<?php
								$showBtn = "disabled";
								if($pdata['current_amt'] >= $MIN_WITHDRAW){ $showBtn = ""; } 
								$fnParams = "'$walletAddress','{$pdata['current_amt']}','$DAPP_PRIVATE_KEY','$RPC_ENDPOINT'";
								?>
							  <button onclick="rewardClaim(<?php echo $fnParams; ?>)" class="btn btn-primary" name="formbtn" <?php echo $showBtn; ?>>Claim Balance</button>
							</div>
						</form>
						<div class="text-center"><br></div>
						<form method="post" action="deposit.php">
							<div class="text-center">
							  <button type="submit" class="btn btn-primary" name="formbtn">Make Deposit</button>
							</div>
						</form>
                    </div>
                </div>
            </div>
          </div>
        </div>
		
		<div class="col-lg-12">
			<div class="card">
				<div class="card-body">
				  <h5 class="card-title"><?php echo $formMsg; ?></h5>
				</div>
			</div>
		</div>
		<div class="col-lg-12">
          <div class="card">
            <div class="card-body">
				<?php
				$vc = "select count(*) from wallet_history where user_id = '$userID'";
				$vres = mysqli_query($conn, $vc);
				$vdata = mysqli_fetch_row($vres);
				$trxCount = $vdata[0];
				?>
              <h5 class="card-title">Wallet History (<?php echo $trxCount; ?>)</h5>
              
              <table class="table">
                <thead>
                  <tr><th scope="col">Sn</th><th scope="col">Date</th><th scope="col">Type</th><th scope="col">Amount</th><th scope="col">Description</th><th scope="col">Transaction ID</th><th scope="col">Poll Title</th></tr>
                </thead>
                <tbody>
					<?php
					$sql = "select reward_amt, poll_id, activity_date, activity_type, activity_desc, trx_id from wallet_history where user_id = '$userID'";
					$res = mysqli_query($conn, $sql);
					$rowCount = 1;
					while($pdata = mysqli_fetch_assoc($res)){
						echo "<tr><th scope='row'>$rowCount</th>";
						echo "<td>{$pdata['activity_date']}</td><td>{$pdata['activity_type']}</td>";
						$amtInSol = $pdata['reward_amt']/$LAMPS_PER_SOL;
						echo "<td>$amtInSol SOL</td><td>{$pdata['activity_desc']}</td><td>{$pdata['trx_id']}</td>";
						if($pdata['poll_id'] > 0){
							$q2 = "select poll_title from polls where poll_id = '{$pdata['poll_id']}'";
							$res2 = mysqli_query($conn, $q2);
							$pdata2 = mysqli_fetch_assoc($res2);
							echo "<td>{$pdata2['poll_title']}</td></tr>";
						}else{
							echo "<td>NA</td></tr>";
						}
						$rowCount++;
					}
					?>
				</tbody>
              </table>
			  
            </div>
          </div>

        </div>
		
		
      </div>
    </section>

  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>
</html>