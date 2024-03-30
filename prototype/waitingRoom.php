<!-- now need to connect database to the waiting room number and player name -->
<!DOCTYPE html>
<html lang="en"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waiting Room</title>
    <link href="../style/waitingRoom.css" rel="stylesheet" type="text/css">
    <style>
</style>

</head>
<body>
    <div class="page-title">Waiting Room</div>
    <?php
        // URL of your Python backend
        $python_backend_url = 'http://localhost:8080';

        // Make a GET request to the Python backend
        $response_json = file_get_contents($python_backend_url);

        // Decode the JSON response into a PHP associative array
        $response_data = json_decode($response_json, true);

        // Check if the response contains player names
        if (isset($response_data['players'])) {
            $player_names = $response_data['players'];
        } else {
            echo "No player names found.";
        }

        // Check if the response contains room num
        if (isset($response_data['room'])){
            $room_num = $response_data['room'];
        } else {
            echo "Room number not found.";
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if(array_key_exists('StartGame', $_POST)) {
                $command = escapeshellcmd('python ./../ES/elo_system_sql.py');
                $output = shell_exec($command);
                echo $output;
            }
        }
    ?>
    <div class="header">
        <button id="leaveRoom" onclick="backToMatchingLobby()">Leave Room</button>
        <p> Room no: <?php echo $room_num ?></p>
        <button id="reportPlayer" onclick="goToReportPage()">Report Player</button>
    </div>
    <div class="container">
        <p> Our Team </p>
        <div class="container">
            <div class="players-grid">
                <div class="player" id="player1">
                    <img src="C:\Users\Mridul\Dropbox\PC\Desktop\Card1.jpg" alt="Player 1 Image" id="player1-img">
                    <p><?php echo $player_names[0] ?></p>
                </div>
                <div class="player" id="player2">
                    <img src="C:\Users\Mridul\Dropbox\PC\Desktop\Card2.jpg" alt="Player 2 Image" id="player2-img">
                    <p><?php echo $player_names[1] ?></p>
                </div>
                <div class="player" id="player3">
                    <img src="C:\Users\Mridul\Dropbox\PC\Desktop\Card1.jpg" alt="Player 3 Image" id="player3-img">
                    <p><?php echo $player_names[2] ?></p>
                </div>
                <div class="player" id="player4">
                    <img src="C:\Users\Mridul\Dropbox\PC\Desktop\Card2.jpg" alt="Player 4 Image" id="player4-img">
                    <p><?php echo $player_names[3] ?></p>
                </div>
            </div>
        </div>
        <br>
        <button id="chatbox" onclick="goToChatBox()">Chat</button>
        <form method="post">
            <input type="submit" name="StartGame" id="play" value="Play" />
        </form>
    </div>
    <div class="footer"></div>
    <script>

        function goToReportPage(){
            window.location.href="reportPage.html";
        }
        
        function backToMatchingLobby(){
            window.location.href="matchingLobby.php";
        }

        function goToChatBox(){
            window.location.href="chatBoxPage.html";
        }


    </script>
</body>
</html>
