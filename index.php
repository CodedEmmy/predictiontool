<?php
ob_start();
include_once("dbconfig.php");
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Login - CrowdWise Prediction Tool</title>
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
  
  <script src="web3libs/solanaweb3.js"></script>
  <script>
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
  
	async function walletLogin()
	{
		const theForm = document.getElementById("loginform");
		theForm.addEventListener("submit",(e) => {e.preventDefault();});
		let loginResp =  await phantomLogin();
		theForm.errmsg.value = loginResp.errMessage;
		theForm.walletaddr.value = loginResp.walletAddress;
		theForm.submit();
	}
  </script>
</head>

<body>

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">

              <div class="d-flex justify-content-center py-4">
                <a href="index.php" class="logo d-flex align-items-center w-auto">
                  <img src="assets/img/logo.png" alt="">
                  <span class="d-none d-lg-block">CrowdWise</span>
                </a>
              </div><!-- End Logo -->

              <div class="card mb-3">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Decision-Making Tool</h5>
                    <p class="text-center small">Login to your account</p>
                  </div>
				  
				  <?php
					$loginErr = false;
					$errormsg = "";
					if(isset($_POST['walletaddr'])){
						$walletAddr = trim(mysqli_real_escape_string($conn,$_POST['walletaddr']));
						$errormsg = trim(mysqli_real_escape_string($conn,$_POST['errmsg']));
						if($walletAddr != "None" && $walletAddr != ""){
							$userID = "";
							$nickname = "";
							$sql = "select user_id, nickname from user_accounts where wallet_address = '$walletAddr'";
							$res = mysqli_query($conn, $sql);
							if(@mysqli_num_rows($res) == 0){
								$nickname = "CW_".substr($walletAddr,0, 7);
								$insQuery = "insert into user_accounts(wallet_address, nickname, current_amt, withdrawn_amt) values('$walletAddr', '$nickname', '0', '0')";
								mysqli_query($conn, $insQuery);
								
								$usql = "select user_id from user_accounts where wallet_address = '$walletAddr'";
								$ures = mysqli_query($conn, $usql);
								$pdata = mysqli_fetch_assoc($ures);
								$userID = $pdata['user_id'];
							}else{
								$pdata = mysqli_fetch_assoc($res);
								$userID = $pdata['user_id'];
								$nickname = $pdata['nickname'];
							}
							session_start();
							$_SESSION['u_id'] = $userID;
							$_SESSION['u_nickname'] = $nickname;
							$_SESSION['w_address'] = $walletAddr;
							header("location: home.php");
						}else{
							$loginErr = true;
							//$errormsg = "No wallet";
						}
					}                
					if($loginErr){
						echo "<div class='pt-4 pb-2' style='color: #cc0000;text-align:center;'>$errormsg</div><hr>";
					}
				?>

                  <form class="row g-3 needs-validation" novalidate method="post" action="index.php" id="loginform">
					<input type="hidden" name="walletaddr" id="walletaddr">
					<input type="hidden" name="errmsg" id="errmsg">
					
                    <div class="col-12">
					  <div class="input-group has-validation">
                        <img src="assets/img/login.jpg" alt="Login image">
                      </div>
                    </div>
                    <div class="col-12">
                      <button class="btn btn-primary w-100" onclick="walletLogin()" id="loginbtn" name="loginbtn">Login/Register</button>
                    </div>
                  </form>

                </div>
              </div>

              <div class="credits">
                (Phantom Solana Wallet Required)<br> [This is a Demo platform: Use Solana Devnet to interact]
              </div>

            </div>
          </div>
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  
  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>