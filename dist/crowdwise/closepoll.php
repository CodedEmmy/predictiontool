<?php
ob_start();
require_once("check.php");
include_once("dbconfig.php");
$userID = $_SESSION['u_id'];
$userName = $_SESSION['u_nickname'];
$walletAddress = $_SESSION['w_address'];
$today = date("Y-m-d H:i:s");

if(isset($_POST['pid'])){
	$pollID = trim(mysqli_real_escape_string($conn,$_GET['pid']));
	$highestCount = -1;
	$highestIndex = 0;
	$totalVote = 0;
	$equalCount = 0;
	$sql = "select vote_count, option_id from poll_options where poll_id = '$pollID'";
	$res = mysqli_query($conn, $sql);
	while($pdata = mysqli_fetch_assoc($res)){
		$totalVote = $totalVote + $pdata['vote_count'];;
		if($pdata['vote_count'] > $highestCount){
			$highestCount = $pdata['vote_count'];
			$highestIndex = $pdata['option_id'];
		}
	}
	$sql = "select vote_count from poll_options where poll_id = '$pollID'";
	$res = mysqli_query($conn, $sql);
	while($pdata = mysqli_fetch_assoc($res)){
		if($pdata['vote_count'] == $highestCount){
			$equalCount++;
		}
	}
	if($equalCount > 1 && $totalVote != 0){
		//Results are inconclusive
		$sql = "update polls set poll_result = '-2', expired_flag = '1' where poll_id = '$pollID'";
		mysqli_query($conn, $sql);
	}else{
		if($totalVote == 0){
			$sql = "update polls set poll_result = '-1', expired_flag = '1' where poll_id = '$pollID'";
			mysqli_query($conn, $sql);
		}else{
			$sql = "update polls set poll_result = '$highestIndex', expired_flag = '1' where poll_id = '$pollID'";
			mysqli_query($conn, $sql);
		}
	}
	
	$sql = "select incentivised, incentive_pool from polls where poll_id = '$pollID'";
	$res = mysqli_query($conn, $sql);
	$pdata = mysqli_fetch_assoc($res);
	if($pdata['incentivised'] == 1){
		if($totalVote == 0){
			$refund = $pdata['incentive_pool'];
			
			$usql = "insert into wallet_history (user_id, reward_amt, poll_id, activity_date, activity_type, activity_desc, trx_id) values('$userID', '$refund', '$pollID', '$today', 'IN', 'Refund for Poll', 'NA')";
			mysqli_query($conn, $usql);
			$usql = "update user_accounts set current_amt = current_amt + $refund where user_id = '$userID'";
			mysqli_query($conn, $usql);
			
		}else{
			$csql = "select count(*) from poll_voters where poll_id = '$pollID'";
			$cres = mysqli_query($conn, $csql);
			$cdata = mysqli_fetch_row($cres);
			$amountPerVoter = $pdata['incentive_pool']/$cdata[0];
			
			$usql = "update poll_voters set reward_amt = '$amountPerVoter', reward_date = '$today' where poll_id = '$pollID'";
			mysqli_query($conn, $usql);
			
			$vsql = "select voter_id from poll_voters where poll_id = '$pollID'";
			$vres = mysqli_query($conn, $vsql);
			while($vdata = mysqli_fetch_assoc($vres)){
				$usql = "insert into wallet_history (user_id, reward_amt, poll_id, activity_date, activity_type, activity_desc, trx_id) values('{$vdata['voter_id']}', '$amountPerVoter', '$pollID', '$today', 'IN', 'Poll Reward', 'NA')";
				mysqli_query($conn, $usql);
				
				$usql = "update user_accounts set current_amt = current_amt + $amountPerVoter where user_id = '{$vdata['voter_id']}'";
				mysqli_query($conn, $usql);
			}
		}
	}
}
$loc = "location: mypolls.php?pid=$pollID";
//header("HTTP/1.1 301 Moved Permanently");
header($loc);
?>