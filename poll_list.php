<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];
$today = date("Y-m-d H:i:s");
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
          <li class="breadcrumb-item active">Voting Poll</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        
		<div class="col-lg-12">
          <div class="card">
            <div class="card-body">
				<h5 class="card-title">Polls <span>| Total</span></h5>
                <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-list"></i>
                    </div>
                    <div class="ps-3">
						<?php
						$sql = "select count(*) from polls";
						$res = mysqli_query($conn, $sql);
						$pdata = mysqli_fetch_row($res);
						echo "<h5>{$pdata[0]}</h5>";
						?>
                    </div>
                </div>
            </div>
          </div>
        </div>
		
		<div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Available Polls</h5>
              
              <table class="table">
                <thead>
                  <tr><th scope="col">Sn</th><th scope="col">Poll Title</th><th scope="col">Duration</th><th scope="col">Incentive</th><th scope="col">Poll Status</th><th scope="col">Vote Status</th><th scope="col">Action</th></tr>
                </thead>
                <tbody>
					<?php
					$sql = "select poll_id, poll_owner, poll_title, start_time, end_time, incentivised, expired_flag from polls";
					$res = mysqli_query($conn, $sql);
					$rowCount = 1;
					while($pdata = mysqli_fetch_assoc($res)){
						$pollOwner = false;
						if($pdata['poll_owner'] == $userID){
							$pollOwner = true;
						}
						echo "<tr><th scope='row'>";
						if($pollOwner){ echo "* "; }
						echo "$rowCount</th>";
						echo "<td>{$pdata['poll_title']}</td><td>{$pdata['start_time']} - <br>{$pdata['end_time']}</td>";
						$rewardPool = $pdata['incentivised'] == 1? "Yes":"No";
						$canVote = true;
						$pollStatus = $pdata['expired_flag'] == 1? "Closed":"Active";
						if($today > $pdata['end_time']){
							$canVote = false;
							$pollStatus = "Closed";
						}
						echo "<td>$rewardPool</td><td>$pollStatus</td>";
						if($pollOwner){
							echo "<td>Can't vote<br>(Poll Owner) </td><td>&nbsp;</td></tr>";
						}else{
							$q2 = "select reward_amt from poll_voters where poll_id = '{$pdata['poll_id']}' and voter_id = '$userID'";
							$res2 = mysqli_query($conn, $q2);
							if(@mysqli_num_rows($res2) == 0){
								if($canVote){
									echo "<td>Not Voted</td><td><a href='vote.php?pid={$pdata['poll_id']}'>Participate</a></td></tr>";
								}else{
									echo "<td>Not Voted</td><td>&nbsp;</td></tr>";
								}
							}else{
								echo "<td>You've Voted</td><td>&nbsp;</td></tr>";
							}
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