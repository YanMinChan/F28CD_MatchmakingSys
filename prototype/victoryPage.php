<?php
	session_start();

	include("../config/config.php");
	if(!isset($_SESSION['valid'])){
		header("Location: index.php");
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Victory</title>
    <link href="../style/victoryPage.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!-- This php code handle all action of the view score button -->
    <?php
        if(isset($_POST['viewScore'])){
            $query = mysqli_query($conn, "UPDATE rooms SET in_game=false, game_result=null WHERE room_num=".$_SESSION['room_num']);
            $query = mysqli_query($conn, "UPDATE rooms SET in_game=false, game_result=null WHERE room_num=".$_SESSION['opposite_team']);
            
            echo "<script>location.href = 'playerScorePage.php';</script>";
        }
    ?>
    <div class="container">
        <div class="players-grid">
            <div class="player" id="player1">
                <img src="../Player 1.jpg" alt="Player 1 Image" id="player1-img">
                <p>Player 1</p>
            </div>
            <div class="player" id="player2">
                <img src="../Player 2.jpg" alt="Player 2 Image" id="player2-img">
                <p>Player 2</p>
            </div>
        </div>
    </div>
    <div class="victory-container">
        <div class="victory-header">
            <h2>Victory</h2>
        </div>
            <div>Team <?php echo $_SESSION['room_num'] ?> vs Team <?php echo $_SESSION['opposite_team'] ?> </div>
        <div class="victory-message">
            <p>Congratulations! You won the game.</p>
        </div>
        <div class="buttons-container">
            <button class="reportPlayer" onclick="goToReportPage()">Report Player</button>
            <form method="post"><button class="viewScore" name="viewScore">View Score</button></form>
        </div>
    </div>
    <div class="container">
        <div class="players-grid">
            <div class="player" id="player3">
                <img src="../Player 3.jpg" alt="Player 3 Image" id="player3-img">
                <p>Player 3</p>
            </div>
            <div class="player" id="player4">
                <img src="../Player 4.jpg" alt="Player 4 Image" id="player4-img">
                <p>Player 4</p>
            </div>
        </div>
    </div>
    <script>
        function goToReportPage(){
            window.location.href = "reportPage.html";
        }

        function goToPlayerScorePage(){
            window.location.href = "playerScorePage.php";
        }
    </script>
</body>
</html>
