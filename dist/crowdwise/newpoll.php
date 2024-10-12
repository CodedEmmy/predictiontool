<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
require_once("appconstants.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];

$showForm1 = true;
$noOfOptions = 2;
$formMsg = "";
if(isset($_POST['form1btn'])){
	$noOfOptions = trim(mysqli_real_escape_string($conn,$_POST['optcount']));
	if($noOfOptions == ""){
		$formMsg = "All fields are required!";
	}else if(!is_numeric($noOfOptions)){
		$formMsg = "Value must be numeric!";
	}else{
		$showForm1 = false;
	}
}
if(isset($_POST['ptitle'])){
	$noOfOptions = trim(mysqli_real_escape_string($conn,$_POST['optcount']));
	$pTitle = trim(mysqli_real_escape_string($conn,$_POST['ptitle']));
	$pResult = trim(mysqli_real_escape_string($conn,$_POST['presult']));
	$pEnd = trim(mysqli_real_escape_string($conn,$_POST['pend']));
	$pReward = trim(mysqli_real_escape_string($conn,$_POST['preward']));
	$pAmount = trim(mysqli_real_escape_string($conn,$_POST['pamt']));
	$optArray = array();
	$missingOption = false;
	for($i = 0;$i < $noOfOptions; $i++){
		$indx = "opt$i";
		$optArray[$i] = trim(mysqli_real_escape_string($conn,$_POST[$indx]));
		if($optArray[$i] == ""){
			$missingOption = true;
		}
	}
	if($pTitle == "" || $pEnd == ""){
		$missingOption = true;
	}
	if($pReward == 1){
		$minIncentive = $MIN_INCENTIVE/$LAMPS_PER_SOL;
		if($pAmount == "" ){
			$missingOption = true;
		}else if(!is_numeric($pAmount)){
			$formMsg = "Incentive Amount must be numeric!";
			$missingOption = true;
		}else if($pAmount < $minIncentive){
			$formMsg = "Incentive Amount must be greater or equal to $minIncentive SOL!";
			$missingOption = true;
		}
	}else{
		$pAmount = 0;
	}
	if($missingOption){
		$formMsg = $formMsg."<br>All fields are required.";
	}else{
		$incentiveAmt = $pAmount * $LAMPS_PER_SOL;
		$totalFee = $incentiveAmt + $POLL_FEE;
		$sql = "select current_amt from user_accounts where user_id = '$userID'";
		$res = mysqli_query($conn, $sql);
		$odata = mysqli_fetch_assoc($res);
		if($odata['current_amt'] >= $totalFee){
			$newAmt = $odata['current_amt'] - $totalFee;
			$q = "update user_accounts set current_amt = $newAmt where user_id = '$userID'";
			mysqli_query($conn, $q);
			$today = date("Y-m-d H:i:s");
			$q = "insert into polls (poll_owner, poll_title, poll_result, result_access, incentivised, incentive_pool, start_time, end_time, expired_flag) values('$userID', '$pTitle', '0', '$pResult', '$pReward', '$incentiveAmt', '$today', '$pEnd', '0')";
			mysqli_query($conn, $q);
			$q = "select poll_id from polls where poll_owner = '$userID' and poll_title = '$pTitle'";
			$res = mysqli_query($conn, $q);
			$pdata2 = mysqli_fetch_assoc($res);
			$pollID = $pdata2['poll_id'];
			for($i = 0;$i < $noOfOptions; $i++){
				$q = "insert into poll_options (poll_id, vote_count, poll_option) values('$pollID', '0', '{$optArray[$i]}')";
				mysqli_query($conn, $q);
			}
			$q = "insert into wallet_history (user_id, reward_amt, poll_id, activity_date, activity_type, activity_desc, trx_id) values('$userID', '$totalFee', '$pollID', '$today', 'OUT', 'Poll Creation Fee', 'NA')";
			mysqli_query($conn, $q);
			$formMsg = "Poll has been successfully created";
		}else{
			$formMsg = "Insufficient funds to cover fees!";
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
  <link rel="stylesheet" href="assets/css/zebra.css" type="text/css">
  
  <!-- javascript functions -->
  <script src="web3libs/solanaweb3.js"></script>
  <script src="web3libs/Base58.min.js"></script>
  <script src="web3libs/nacl-fast.js"></script>
  <script>
	async function signInWallet(walletAddr, theMsg)
	{
		let verified = false;
		const encMessage = new TextEncoder().encode(theMsg);
		try{
			const signedMessage = await window.solana.signMessage(encMessage, "utf8");
			verified = nacl.sign.detached.verify(encMessage, signedMessage.signature, signedMessage.publicKey.toBytes());
			if(walletAddr != signedMessage.publicKey){
				verified = false;
			}
		}catch(err){
			verified = false;
		}
		return verified;
	}
	
	async function signWallet(waddr)
	{
		const theForm = document.getElementById("newform");
		theForm.addEventListener("submit",(e) => {e.preventDefault();});
		const result = await signInWallet(waddr, "Verify your Account. This does not cost any fee.");
		if(result){
			theForm.submit();
		}else{
			alert("Account could not be verified");
		}
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
      <h1>Decision Poll</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item active">Create New Poll</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
		<div class="row">
			<div class="col-lg-12">
				<div class="card">
					<div class="card-body">
					  <h5 class="card-title"><span style="color:#600"><?php echo $formMsg; ?></span></h5>
					  <?php $pFee = $POLL_FEE/$LAMPS_PER_SOL; ?>
					  <p>Poll creation fee: <?php echo $pFee; ?> SOL</p>
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
						<h5 class="card-title">Poll <span>| Create</span></h5>
						<div class="d-flex align-items-center">
							<div class="ps-3">
								<div class="text-center"><br></div>
								<form method="post" action="newpoll.php">
									<div class="row mb-3">
									  <label for="input2" class="col-sm-6 col-form-label">Number of Options</label>
									  <div class="col-sm-6">
										<input type="text" class="form-control" id="input2" name="optcount" value="<?php echo $noOfOptions; ?>">
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
					<h5 class="card-title">Poll <span>| Create</span></h5>
						
					<form method="post" action="newpoll.php" id="newform">
						<input type="hidden" name="optcount" value="<?php echo $noOfOptions; ?>">
						<div class="row mb-3">
						  <label for="inputText" class="col-sm-2 col-form-label">Poll Title/Topic</label>
						  <div class="col-sm-10">
							<input type="text" class="form-control" name="ptitle">
						  </div>
						</div>
						<?php
						for($i = 0;$i < $noOfOptions;$i++){
							$oname = "opt$i";
							$oTitle = "Option ".($i + 1);
							?>
							<div class="row mb-3">
							  <label for="inputText" class="col-sm-2 col-form-label"><?php echo $oTitle; ?></label>
							  <div class="col-sm-10">
								<input type="text" class="form-control" name="<?php echo $oname; ?>">
							  </div>
							</div>
							<?php
						}
						?>
						<div class="row mb-3">
						  <label for="inputText" class="col-sm-2 col-form-label">Poll End Date</label>
						  <div class="col-sm-10">
							<input type="text" class="form-control" name="pend" id="pend">
						  </div>
						</div>
						
						<fieldset class="row mb-3">
						  <legend class="col-form-label col-sm-2 pt-0">Results Access</legend>
						  <div class="col-sm-10">
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="presult" id="presult1" value="Private" checked>
							  <label class="form-check-label" for="presult1">Private</label>
							</div>
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="presult" id="presult2" value="Public">
							  <label class="form-check-label" for="presult2">Public</label>
							</div>
						  </div>
						</fieldset>
						<fieldset class="row mb-3">
						  <legend class="col-form-label col-sm-2 pt-0">Incentivised</legend>
						  <div class="col-sm-10">
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="preward" id="preward1" value="1">
							  <label class="form-check-label" for="presult1">Yes</label>
							</div>
							<div class="form-check">
							  <input class="form-check-input" type="radio" name="preward" id="preward2" value="0" checked>
							  <label class="form-check-label" for="presult2">No</label>
							</div>
						  </div>
						</fieldset>
						<div class="row mb-3">
						  <label for="inputText" class="col-sm-2 col-form-label">Incentive Amount</label>
						  <div class="col-sm-10">
							<input type="text" class="form-control" name="pamt" id="pamt">
						  </div>
						</div>
						<div class="text-center">
						  <button onclick="signWallet('<?php echo $walletAddress; ?>')" class="btn btn-primary" name="formbtn">Sign and Create</button>
						</div>
					</form>
					
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
  <script type="text/javascript" src="assets/js/jquery.min.js"></script>
  <script type="text/javascript" src="assets/js/zebra_datepicker.js"></script>
  <script>
  $(document).ready(function(){
		$('input#pend').Zebra_DatePicker({
			format: 'Y-m-d',
			view: 'years'
		});
	  });
  </script>
</body>
</html>