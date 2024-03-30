<?php
	session_start();

	include("../config/config.php");
	if(!isset($_SESSION['valid'])){
		header("Location: index.php");
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
	<div class="page-title">Matching Lobby</div>
	<div class="header">
		<a href="../config/logout.php"><button id="disconnect">Log out</button></a>
	</div>
	<div class="container">
		<?php
			
			$pname = $_SESSION['valid'];
			$query = mysqli_query($conn, "SELECT * FROM players WHERE name='$pname'");

			while($res = mysqli_fetch_assoc($query)){
				$res_pname = $res['name'];
			}

		?>

		<div>
			<h2>Matching Lobby</h2>
		</div>
		<div>
			<p> Hello <b> <?php echo $res_pname ?></b><p>
		</div>

		<div class="images-container">
			<div id="image1">
				<img src="../playing-video-games.jpg" alt="Random Match">
				<!-- <p class="playerLabel">Player 1</p> -->
				<button id="randomMatch" onclick="goToRandomMatch()">Random Match</button>
			</div>
			<div id="image2">
				<img src="../join-room.jpg" alt="Join Room">
				<!-- <p class="playerLabel">Player 2</p> -->
				<button id="joinRoom" onclick="goToJoinRoom()">Join Room</button>
			</div>
		</div>
	</div>
	<script>
		function goToRandomMatch(){
			window.location.href="waitingRoom.php"
		}
		function goToJoinRoom(){
			window.location.href="joinRoom.php"
		}

	</script>
</body>
</html>
