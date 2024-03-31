<?php
include("config.php");

function leaveRoom(){

    global $conn;

    $query = mysqli_query($conn, "SELECT * FROM rooms WHERE room_num=".$_SESSION['room_num']);
    $room_info = mysqli_fetch_assoc($query);
    $player_count = $room_info['player_count'];
    $team_elo = $room_info['team_elo'];

    $query = mysqli_query($conn, "SELECT * FROM players WHERE name='".$_SESSION['username']."'");
    $current_player_elo = mysqli_fetch_assoc($query)['elo_rating'];

    // update rooms table
    $new_player_count = $player_count - 1;
    
    // remove player from player_joinroom table
    $query = mysqli_query($conn, "DELETE FROM player_joinroom WHERE pname='".$_SESSION['username']."'");

    // delete room if there are no more people in the room
    if ($new_player_count == 0){
        $query = mysqli_query($conn, "DELETE FROM rooms WHERE room_num=".$_SESSION['room_num']);
    } else {
        $new_team_elo = (($team_elo * $player_count) - $current_player_elo)/$new_player_count;
        $query = mysqli_query($conn, "UPDATE rooms SET team_elo=$new_team_elo, player_count=$new_player_count WHERE room_num=".$_SESSION['room_num']);
    }
    
    // update session array
    unset($_SESSION['room_num']);
    unset($_SESSION['player_count']);
    unset($_SESSION['team_elo']);
}

?>