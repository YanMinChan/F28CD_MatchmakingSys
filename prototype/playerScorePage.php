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
    <title>Player Score Page</title>
    <link href="../style/playerScorePage.css" rel="stylesheet" type="text/css">
</head>
<body>
    <!-- This php code will handle the leave room button -->
    <?php
        include("../config/leaveRoom.php");
        if(isset($_POST['leavePage'])){
            leaveRoom();
            echo "<script>location.href = 'matchingLobby.php';</script>";
        }
    ?>
    <div class="header">
        <form method="post"><button id="leavePage" name="leavePage">Leave</button></form>
        <button id="reportPlayer" onclick="goToReportPage()">Report Player</button>
    </div>
    <div class="container">
        <h2 id="containerHeading">Scoreboard</h2>
        <div class="player-score" id="you">
            <p id="p1">Player 1 Score</p>
            <p id="p1-score">100</p> 
        </div>
        <div class="player-score">
            <p id="p2">Player 2 Score</p>
            <p id="p2-score">200</p> 
        </div>
        <div class="player-score">
            <p id="p3">Player 3 Score</p>
            <p id="p3-score">150</p>
        </div>
        <div class="player-score">
            <p id="p4">Player 4 Score</p>
            <p id="p4-score">180</p> 
        </div>
    <div class="player-score">
        <p id="p5">Player 5 Score</p>
        <p id="p5-score">100</p>
    </div>
    <div class="player-score">
        <p id="p6">Player 6 Score</p>
        <p id="p6-score">250</p>
    </div>
    <div class="player-score">
        <p id="p7">Player 7 Score</p>
        <p id="p7-score">125</p> 
    </div>
    <div class="player-score">
        <p id="p8">Player 8 Score</p>
        <p id="p8-score">190</p>
    </div>
</div>
    <div class="footer">
        <button id="chatBox" onclick="goToChatBox()">Chat</button>
        <button id="playAgain" onclick="goToWaitingRoom()">Play Again</button>
    </div>

    <script>
        function goToReportPage(){
            window.location.href="reportPage.html";
        }

        function goToChatBox(){
            window.location.href="chatBoxPage.html";
        }

        function goToWaitingRoom(){
            window.location.href="waitingRoom.php";
        }
    </script>
</body>
</html>
