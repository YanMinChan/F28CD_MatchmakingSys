<?php
	session_start();

	include("../config/config.php");
	if(!isset($_SESSION['valid'])){
		header("Location: index.php");
	}
?>
<!DOCTYPE html>
<head>
    <title>Join Room</title>
	<link href="../style/joinRoom.css" rel="stylesheet" type="text/css" >
</head>
<body>
    <div class="header">
        <button id="back" onclick="backToMatchingLobby()">Back</button>
    </div>
    <div class="container">
        <?php
            if (isset($_POST['joinRoom'])){
                $room_num = $_POST['roomNum'];

                //execute query
                $sql = $conn->prepare("SELECT * FROM rooms WHERE room_num=?");
                $sql->bind_param("s", $room_num);
                $sql->execute();
                $result = $sql->get_result();
                $row = $result-> fetch_assoc();

                // check if room num exist
                if ($result->num_rows==0){ 
                    echo "<div class='interface'>
                        <p> This room does not exist! </p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button id='roomne'> Go Back </button>";
                } else if ($row['player_count'] == 5){
                    echo "<div class='interface'>
                        <p> This room is full! </p>
                    </div> <br>";
                    echo "<a href='javascript:self.history.back()'><button id='roomne'> Go Back </button>";
                }
                else {

                    // successfully join room, insert player into room
                    $sql = $conn->prepare("INSERT INTO player_joinroom (room_num, pname) VALUES (?, ?)");
                    $sql->bind_param("ds", $room_num, $_SESSION['username']);
                    $sql->execute();

                    
                    $query = mysqli_query($conn, "SELECT * FROM players WHERE name='".$_SESSION['username']."'");
                    $current_player_elo = mysqli_fetch_assoc($query)['elo_rating'];

                    $_SESSION['room_num'] = $room_num;
                    $_SESSION['player_count'] = $row['player_count'];
                    $_SESSION['team_elo'] = ($row['team_elo'] * $_SESSION['player_count'] + $current_player_elo)/($_SESSION['player_count'] + 1);

                    $query = mysqli_query($conn, "UPDATE rooms SET team_elo=".$_SESSION['team_elo'].", player_count=(".$_SESSION['player_count']." + 1) WHERE room_num=".$_SESSION['room_num']);

                    // check for success or error
                    if ($sql->affected_rows > 0) {
                        echo "<div>
                            <p> Successfully join room! </p>
                        </div> <br>";
                        $room_url = "waitingRoom.php?room_num=" . strval($_SESSION['room_num']);
                        echo "<a href='$room_url'><button> Continue </button>";
                    } else {
                        echo "Error joining room: " . $sql->error . "<br>";
                        echo "<a href='javascript:self.history.back()'><button> Go Back </button>";
                    }
                }
            } else if (isset($_POST['createRoom'])){
                // check room num that exist
                $sql = mysqli_query($conn, "SELECT room_num FROM rooms");
                $rooms = mysqli_fetch_assoc($sql);
                sort($rooms);

                // generate random room num and check if it exist
                $room_num = rand(1000, 2000);
                for ($i = 0; $i < count($rooms); $i++){
                    if ($room_num==$rooms[$i]){
                        $room_num += 1;
                    }
                }
                $_SESSION['room_num']=$room_num;

                // store room_num and creator
                //$sql = $conn->prepare("INSERT INTO player_joinroom (room_num, pname) VALUES (?, ?)");
                //$sql->bind_param("ds", $_SESSION['room_num'], $_SESSION['username']);
                //$sql->execute();
                    
                $sql = $conn->prepare("INSERT INTO rooms (room_num, creator, team_elo) VALUES (?, ?, ?)");
                $sql->bind_param("dsd", $_SESSION['room_num'], $_SESSION['username'], $_SESSION['elo']);
                $sql->execute();
                
                $query = mysqli_query($conn, "INSERT INTO player_joinroom (room_num, pname) VALUES (".$_SESSION['room_num'].",'".$_SESSION['username']."')");


                // check for success or error
                if ($sql->affected_rows > 0) {
                    echo "<div>
                        <p> Successfully create room! </p>
                    </div> <br>";
                    $room_url = "waitingRoom.php?room_num=" . strval($_SESSION['room_num']);
                    echo "<a href='$room_url'><button> Continue </button>";
                } else {
                    echo "Error creating room: " . $sql->error . "<br>";
                    echo "<a href='javascript:self.history.back()'><button> Go Back </button>";
                }
            }
            else{
        ?>
        <div class="interface">
            <p>Join Room</p>
            <br>
            <form id="joinroom" action="" method="post">
                <label for="enterRoomNum">Enter Room Number:</label>
                <input type="text" id="enterRoomNum" name="roomNum" placeholder="Room Number" required>
                <input type="submit" name="joinRoom" hidden />
            </form>
            <p>or</p>
            <form action="" method="post">
                <button id="createRoom" name="createRoom">Create Room</button>
            </form>
        </div>
        <?php } ?>
    </div>
    
    <div class="footer">

    </div>
    <script>
        function backToMatchingLobby() {
            window.location.href="matchingLobby.php";
        }
        // function goToWaitingRoom() {
        //     window.location.href="waitingRoom.php";
        // }
    </script>
</body>
</html>
