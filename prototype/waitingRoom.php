<?php
	session_start();

	include("../config/config.php");
	if(!isset($_SESSION['valid'])){
		header("Location: index.php");
	}
?>
<!DOCTYPE html>
<html lang="en"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="10" />
    <title>Waiting Room</title>
    <link href="../style/waitingRoom.css" rel="stylesheet" type="text/css">
    <style>
</style>

</head>
<body>
    <div class="page-title">Waiting Room</div>
    <!-- This php code handle all action of the play button and starting the game -->
    <?php
        // check if the game has started. If it is then move into game
        $query = mysqli_query($conn, "SELECT * FROM rooms WHERE room_num=".$_SESSION['room_num']);
        $res = mysqli_fetch_assoc($query);     

        if($res['in_game']){
            //change
            if($res['game_result']==1){
                echo "<script>location.href = 'victoryPage.php';</script>";
            } else {
                echo "<script>location.href = 'defeatPage.php';</script>";
            }
        }

        if (isset($_POST['StartGame'])) {
            // start game if room is full
            if ($res['player_count']==5){
                include("../ES/elo_system.php");
                list($_SESSION['opposite_team'], $match_result) = run($_SESSION['room_num']);

                //to go to a new page
                if($match_result==1){
                    echo "<script>location.href = 'victoryPage.php';</script>";
                } else {
                    echo "<script>location.href = 'defeatPage.php';</script>";
                }
               
                exit;
            }


        }
    ?>
    <!-- This php code fetch all player in current room -->
    <?php
        $query = mysqli_query($conn, "SELECT * FROM player_joinroom WHERE room_num=".$_SESSION['room_num']." AND pname NOT LIKE '".$_SESSION['username']."'");
        if ($query){
            // check if there are players in room
            if (mysqli_num_rows($query) > 0){
                while($row = mysqli_fetch_assoc($query)){
                    $players[] = $row['pname'];
                }

            } else {
                $res = "No players"; 
            }
        }
    ?>
    
    <!-- This php code handles all action of the leave room button -->
    <?php
        include("../config/leaveRoom.php");
        if(isset($_POST['leaveRoom'])){
            leaveRoom();

            // url to matching lobby
            echo "<script>location.href = 'matchingLobby.php';</script>";
        }
    ?>

    
    <div class="header">
        <form method="post"><button id="leaveRoom" name="leaveRoom">Leave Room</button></form>
        <p> Room no: <?php echo $_SESSION['room_num'] ?></p>
        <button id="reportPlayer" onclick="goToReportPage()">Report Player</button>
    </div>
    <div class="container">
        <p> Our Team </p>
        <div class="container">
            <div class="players-grid">
                <div class="player" id="player1">
                    <img src="../Player 1.jpg" alt="Player 1 Image" id="player1-img">
                    <p><?php echo $_SESSION['username'] ?></p>
                </div>
                <div class="player" id="player2">
                    <img src="../Player 2.jpg" alt="Player 2 Image" id="player2-img">
                    <p><?php if(!empty($players[0])){echo $players[0];} else {echo "No player";} ?></p>
                </div>
                <div class="player" id="player3">
                    <img src="../Player 3.jpg" alt="Player 3 Image" id="player3-img">
                    <p><?php if(!empty($players[1])){echo $players[1];} else {echo "No player";} ?></p>
                </div>
                <div class="player" id="player4">
                    <img src="../Player 4.jpg" alt="Player 4 Image" id="player4-img">
                    <p><?php if(!empty($players[2])){echo $players[2];} else {echo "No player";} ?></p>
                </div>
                <div class="player" id="player5">
                    <img src="../Player 5.jpg" alt="Player 4 Image" id="player4-img">
                    <p><?php if(!empty($players[3])){echo $players[3];} else {echo "No player";} ?></p>
                </div>
            </div>
        </div>
        <br>
        <button id="chatbox" onclick="goToChatBox()">Chat</button>
        <form method="post">
            <button type="submit" name="StartGame" id="play">Play</button>
        </form>
    </div>
    <div class="footer"></div>
    <script>

        function goToReportPage(){
            //function to go to a new web page, to report players
            window.location.href="reportPage.html";
        }

        function goToChatBox(){
            //function to go to a new web page, for chat with other player
            window.location.href="chatBoxPage.html";
        }
    </script>
</body>
</html>
