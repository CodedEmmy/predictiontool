<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
require_once("appconstants.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];
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
			if(walletAddr != signMessage.publicKey){
				verified = false;
			}
		}catch(err){
			verified = false;
		}
		return verified;
	}

	async function signWallet(waddr)
	{
		const theForm = document.getElementById("closeform");
		theForm.addEventListener("submit",(e) => {e.preventDefault();});
		const result = await signInWallet(waddr, "Close your Poll. This does not cost any fee.");
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
      <h1>Polls</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="home.php">Home</a></li>
          <li class="breadcrumb-item active">My Polls</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-7">
          <div class="card">
            <div class="card-body">
				<?php
				$sql = "select count(*) from polls where poll_owner = '$userID'";
				$cres = mysqli_query($conn, $sql);
				$cdata = mysqli_fetch_row($cres);
				?>
              <h5 class="card-title">My Created Polls (<?php echo "{$cdata[0]}"; ?>)</h5>
              
              <table class="table">
                <thead>
                  <tr><th scope="col">Sn</th><th scope="col">Poll Title</th><th scope="col">Poll Status</th><th scope="col">Details</th></tr>
                </thead>
                <tbody>
					<?php
					$sql = "select poll_id, poll_title, expired_flag from polls where poll_owner = '$userID'";
					$res = mysqli_query($conn, $sql);
					$rowCount = 1;
					while($pdata = mysqli_fetch_assoc($res)){
						echo "<tr><th scope='row'>$rowCount</th>";
						echo "<td>{$pdata['poll_title']}</td>";
						$pollStatus = $pdata['expired_flag'] == 1? "Closed":"Active";
						echo "<td>$pollStatus</td>";
						echo "<td><a href='mypolls.php?pid={$pdata['poll_id']}'>View Details</a></td></tr>";
						$rowCount++;
					}
					?>
				</tbody>
              </table>
			  
            </div>
          </div>

        </div>
		
		<div class="col-lg-5">
          <div class="card info-card sales-card">
            <div class="card-body">
                <h5 class="card-title">Poll <span>| Details</span></h5>
				<?php
				if(isset($_GET['pid'])){
					$pollID = trim(mysqli_real_escape_string($conn,$_GET['pid']));
					$sql = "select * from polls where poll_id = '$pollID'";
					$res = mysqli_query($conn, $sql);
					if(@mysqli_num_rows($res) != 0){
						$pdata = mysqli_fetch_assoc($res);
						$osql = "select * from poll_options where poll_id = '$pollID'";
						$ores = mysqli_query($conn, $osql);
					?>
						<table class="table">
						<thead><tr><th colspan=2><?php echo $pdata['poll_title']; ?></th></tr>
						</thead>
						<tbody>
						<?php
						$hasClosed = $pdata['expired_flag'] == 1?true:false;
						$hasReward = $pdata['incentivised'] == 1?true:false;
						echo "<tr><th>Options</th><td><ol>";
						while($odata = mysqli_fetch_assoc($ores)){
							echo "<li>{$odata['poll_option']}";
							if($hasClosed){
								echo " ({$odata['vote_count']})";
							}
							echo "</li>";
						}
						echo "</ol></td></tr>";
						echo "<tr><th>Duration</th><td>{$pdata['start_time']} - {$pdata['end_time']}</td></tr>";
						echo "<tr><th>Result Access</th><td>{$pdata['result_access']}</td></tr>";
						echo "<tr><th>Incentivised</th><td>";
						if($hasReward){
							echo "Yes</td></tr>";
							$rewardAmt = $pdata['incentive_pool']/$LAMPS_PER_SOL;
							echo "<tr><th>Reward Pool</th><td>$rewardAmt SOL</td></tr>";
						}else{
							echo "No</td></tr>";
							echo "<tr><th>Reward Pool</th><td>N/A</td></tr>";
						}
						echo "<tr><th>Poll Status</th><td>";
						if($hasClosed){
							echo "Closed</td></tr>";
							echo "<tr><th>Poll Result</th><td>";
							if($pdata['poll_result'] > 0){
								$psql = "select poll_option from poll_options where option_id = '{$pdata['poll_result']}'";
								$pres = mysqli_query($conn, $psql);
								$rdata = mysqli_fetch_assoc($pres);
								echo $rdata['poll_option'];
								echo "</td></tr>";
							}else if($pdata['poll_result'] == -1){
								echo "Not valid (No votes)</td></tr>";
							}else if($pdata['poll_result'] == -2){
								echo "Inconclusive</td></tr>";
							}else if($pdata['poll_result'] == 0){
								echo "Pending</td></tr>";
							}
						}else{
							echo "Active</td></tr>";
							echo "<tr><th>Poll Result</th><td>Pending</td></tr>";
						}
						if(!$hasClosed){
							echo "<tr><td colspan=2>";
							?>
							<form method="post" action="closepoll.php" id="closeform">
								<input type="hidden" name="pid" value="<?php echo $pdata['poll_id']; ?>">
								<div class="text-center">
								  <button onclick="signWallet('<?php echo $walletAddress; ?>')" class="btn btn-primary" name="formbtn">Close Poll</button>
								</div>
							</form>
							<?php
							echo "</td></tr>";
						}
						?>
						</tbody>
						</table>
						<?php
					}else{
						?>
						<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
						  <i class="bi bi-list-ul"></i>
						</div>
						<?php
					}
				}else{
					?>
					<div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-list-ol"></i>
                    </div>
					<?php
				}
				?>
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