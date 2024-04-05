<?php
	session_start();

	include("../config/config.php");
	if(!isset($_SESSION['valid'])){
		header("Location: index.php");
	}

	include("../config/leaveRoom.php");
	// check if player unexpectedly leave room without pressing the leave room button in waiting room
	$query = mysqli_query($conn, "SELECT * FROM player_joinroom WHERE pname='".$_SESSION['username']."'");
	if($query){
		// return results more than 1 rows means user is in room
		if(mysqli_num_rows($query)>0){
			$_SESSION['room_num'] = mysqli_fetch_assoc($query)['room_num'];
			leaveRoom();
		}
	}
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Matching Lobby</title>
	<link href="../style/matchingLobby.css" rel="stylesheet" type="text/css">
	<style>
		img {
			width: 280px; 
			margin-top: 20px;
			height: 350px; 
			display: block; 
			margin: 5 auto; 
			border: 2px solid #34951e; 
			border-radius: 10px; 
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); 
		}
		.description {
			text-align: center;
			margin-top: 10px; 
		}
		.player-label {
			text-align: left;
			margin-top: 10px;
			font-weight: bold;
		}
	</style>
</head>
<body>
	<div class="header">
		<a href="../config/logout.php"><button id="disconnect">Log out</button></a>
	</div>
	<div class="container">
		<!-- This php code selects closest room on click and send user there. -->
		<?php
			include ("../config/createRoom.php");
			
			if(isset($_POST['goToRandomMatch'])){
				$query = mysqli_query($conn, "SELECT * FROM players WHERE name='".$_SESSION['username']."'");
				$current_player_elo = mysqli_fetch_assoc($query)['elo_rating'];
				$query = mysqli_query($conn, "SELECT * FROM rooms WHERE player_count < 5 ORDER BY ABS(team_elo - $current_player_elo) LIMIT 1");
				$res = mysqli_fetch_assoc($query);

				$threshold = 3000;

				// if no room exist create one
				if (!$res){
					createRoom();
				} else { // rooms exist
					// check the elo_threshold, if within threshold move to room, else create new room
					if (abs($res['team_elo'] - $current_player_elo) < $threshold) {
						$_SESSION['room_num'] = $res['room_num'];
						$_SESSION['player_count'] = $res['player_count'];
						$_SESSION['team_elo'] = ($res['team_elo'] * $_SESSION['player_count'] + $current_player_elo)/($_SESSION['player_count'] + 1);
						
						// update team elo
						$query = mysqli_query($conn, "UPDATE rooms SET team_elo=".$_SESSION['team_elo'].", player_count=(".$_SESSION['player_count']." + 1) WHERE room_num=".$_SESSION['room_num']);
					} else {
						createRoom();
					}
				}

				// store room_num and player name into another table
				if (isset($_SESSION['room_num'])){
					// store room_num and player name into another table
					$sql = $conn->prepare("INSERT INTO player_joinroom (room_num, pname) VALUES (?, ?)");
					$sql->bind_param("ds", $_SESSION['room_num'], $_SESSION['username']);
					$sql->execute();

					// url to the waiting room
					$room_url = "waitingRoom.php?room_num=" . strval($_SESSION['room_num']);
					echo "<script>location.href='$room_url';</script>";
				}
			}

		?>
		<div>
		<div class="page-title">Matching Lobby</div>
		</div>
		<div>
			<p> Hello <b> <?php echo $_SESSION['username'] ?></b><p>
		</div>

		<div class="images-container">
			<div id="image1">
				<img src="../playing-video-games.jpg" alt="Random Match">
				<form method="post"><button id="randomMatch" name="goToRandomMatch">Random Match</button></form>
			</div>
			<div id="image2">
				<img src="../join-room.jpg" alt="Join Room">
				<button id="joinRoom" onclick="goToJoinRoom()">Join Room</button>
			</div>
		</div>
	</div>
	<script>
		function goToJoinRoom(){
			window.location.href="joinRoom.php"
		}

	</script>
</body>
</html>
