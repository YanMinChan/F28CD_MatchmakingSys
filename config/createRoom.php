<?php
include("config.php");
function createRoom(){
    global $conn;

    // check room num that exist
    $sql = mysqli_query($conn, "SELECT room_num FROM rooms");
    $rooms = mysqli_fetch_assoc($sql);
    
    // generate unique room number
    if (!$rooms){
        $room_num = rand(1000, 2000);
    } else { 
        sort($rooms);
        // generate random room num and check if it exist
        $room_num = rand(1000, 2000);
        for ($i = 0; $i < count($rooms); $i++){
            if ($room_num==$rooms[$i]){
                $room_num += 1;
            }
        }
    }

    // set up session variables
    $_SESSION['room_num'] = $room_num;
    $_SESSION['player_count'] = 1;
    $_SESSION['team_elo'] = $_SESSION['elo'];

    // store room_num and creator
    $sql = $conn->prepare("INSERT INTO rooms (room_num, creator, team_elo) VALUES (?, ?, ?)");
    $sql->bind_param("dsd", $_SESSION['room_num'], $_SESSION['username'], $_SESSION['elo']);
    $sql->execute();
}
?>