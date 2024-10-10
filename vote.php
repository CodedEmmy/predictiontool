<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
require_once("appconstants.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];

$pollID = "";
$formMsg = "";
$showForm = false;
if(isset($_GET['pid'])){
	$pollID = trim(mysqli_real_escape_string($conn,$_GET['pid']));
	$q = "select vote_date from poll_voters where poll_id = '$pollID' and voter_id = '$userID'";
	$res = mysqli_query($conn, $q);
	if(@mysqli_num_rows($res) == 0){
		$showForm = true;
	}else{
		$formMsg = "Already voted for this poll";
	}
}
if(isset($_POST['pollid'])){
	$pollID = trim(mysqli_real_escape_string($conn,$_POST['pollid']));
	$rewDate = trim(mysqli_real_escape_string($conn,$_POST['rewarddate']));
	$selOption = trim(mysqli_real_escape_string($conn,$_POST['poption']));

	$sql = "update poll_options set vote_count = vote_count + 1 where option_id = '$selOption' and poll_id = '$pollID'";
	mysqli_query($conn, $sql);
	$voteDate = date("Y-m-d H:i:s");
	$sql = "insert into poll_voters(poll_id, voter_id, reward_amt, reward_date, vote_date) values('$pollID', '$userID', '0', '$rewDate', '$voteDate')";
	mysqli_query($conn, $sql);
	$formMsg = "Your vote has been registered";
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
		const theForm = document.getElementById("voteform");
		theForm.addEventListener("submit",(e) => {e.preventDefault();});
		const result = await signInWallet(waddr, "Verify your Vote. This does not cost any fee.");
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
      <h1>Voting Board</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item active">Vote</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
	  <?php
	  if($showForm){
	  ?>
		<div class="col-lg-12">
			<?php
			$sql = "select poll_title, end_time, incentive_pool from polls where poll_id = '$pollID'";
			$res = mysqli_query($conn, $sql);
			$pdata = mysqli_fetch_assoc($res);
			?>

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Topic: <?php echo $pdata['poll_title']; ?></h5>
			  <span style="color:#036"><?php echo $formMsg; ?></span><br><br>

              <form method="post" action="vote.php" id="voteform">
				<div class="row mb-3">
                  <label class="col-sm-12 col-form-label"><strong>Reward Pool:</strong> <?php  echo ($pdata['incentive_pool']/$LAMPS_PER_SOL); echo " SOL"; ?></label>
                </div>
                <input type="hidden" name="pollid" value="<?php echo $pollID; ?>">
				<input type="hidden" name="rewarddate" value="<?php echo $$pdata['end_time']; ?>">
				<fieldset class="row mb-3">
				  <legend class="col-form-label col-sm-3 pt-0"><strong>Topic Options<strong></legend>
				  <div class="col-sm-9">
				<?php
				$opts = "select poll_option,option_id from poll_options where poll_id = '$pollID'";
				$optsRes = mysqli_query($conn, $opts);
				$count = 0;
				while($odata = mysqli_fetch_assoc($optsRes)){
				?>
					<div class="form-check">
					  <input class="form-check-input" type="radio" name="poption" id="presult1" value="<?php echo $odata['option_id']; ?>" <?php if($count == 0) echo "checked"; ?>>
					  <label class="form-check-label" for="presult1"><?php echo $odata['poll_option']; ?></label>
					</div>
				<?php
					$count++;
				}
				?>
					</div>
				</fieldset>
				<div class="text-center"><br></div>
                <div class="text-center">
                  <button onclick="signWallet('<?php echo $walletAddress; ?>')" class="btn btn-primary" name="formbtn">Vote</button>
                </div>
              </form>

            </div>
          </div>

        </div>
		<?php
	  }else{
		  ?>
		  <div class="card info-card sales-card">
            <div class="card-body">
                <h5 class="card-title">Poll <span>| Vote</span></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-file-earmark-check"></i>
                    </div>
                    <div class="ps-3">
						<?php echo $formMsg;?>
                    </div>
                </div>
            </div>
          </div>
		  
		  <?php
	  }
		?>
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