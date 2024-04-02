<?php
// We are going to select 2 teams with closest team elo and match them
// Input: room number
// Action: generate score and store them in sql
// Output: game result(not implemented yet) and opposite team room number

// Load player data from Database
function load_players_sql($room_num) {
    try {
        // connect to the database
        include("../config/config.php");

        // Extract player list
        $sql = "SELECT * FROM players p JOIN player_joinroom pj ON p.name = pj.pname WHERE pj.room_num=$room_num";
        $result = $conn->query($sql);
        $data = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $player = [
                    'name' => $row['name'],
                    'combat_score' => $row['combat_score'],
                    'elo_rating' => (float)$row['elo_rating'],
                    'kills' => $row['kills'],
                    'deaths' => $row['deaths'],
                    'room_num' => $row['room_num']
                ];
                $data[] = $player;
            }
        }

        // Extract team elo
        $sql = "SELECT team_elo FROM rooms WHERE room_num=$room_num";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();
        $team_elo = $row['team_elo'];

        // Set in game to true
        $sql = "UPDATE rooms SET in_game=true WHERE room_num=$room_num";
        $conn->query($sql);

        $conn->close();

        return [$data, $team_elo];
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Find a suitable opposite team
function load_opposite_sql($team_elo, $room_num) {
    try {
        // connect to the database
        include("../config/config.php");

        // Find a suitable team
        $sql = "SELECT room_num FROM rooms WHERE room_num != $room_num AND in_game = false AND player_count = 5 ORDER BY ABS(team_elo-$team_elo) LIMIT 1";
        $result = $conn->query($sql);
        $opposite_team = null;
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $opposite_team = $row['room_num'];

            // Fetch opposite team data
            list($opposite_data, $opposite_team_elo) = load_players_sql($opposite_team);

            $conn->close();

            return [$opposite_data, $opposite_team_elo, $opposite_team];
        } else {
            $conn->close();
            return null;
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Save player data to database
function save_players_sql($players, $teams) {
    try {
        // connect to the database
        include("../config/config.php");

        // Update query
        foreach ($players as $player) {
            $update_query = "UPDATE players SET elo_rating = ? WHERE name = ?";
            $new_elo = $player['elo_rating'];
            $player_name = $player['name'];
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("ds", $new_elo, $player_name);
            $stmt->execute();
        }

        // Update team elo
        foreach ($teams as $team) {
            $update_query = "UPDATE rooms SET team_elo = ? WHERE room_num = ?";
            $new_team_elo = $team['team_elo'];
            $room_num = $team['room_num'];
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("di", $new_team_elo, $room_num);
            $stmt->execute();
        }

        $conn->close();
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Calculate expected outcome of a match
function calculate_expected_outcome($ra, $rb, $c = 400) {
    $qa = pow(10, ($ra / $c));
    $qb = pow(10, ($rb / $c));
    return $qa / ($qa + $qb);
}

// Update Elo rating after a match considering individual performance
function update_elo_with_performance($ra, $sa, $ea, $pa, $pb, $kills, $deaths, $k = 32, $l = 1, $v = 5) {
    $performance_factor = ($kills - $deaths) / 10.0; // Assuming kills and deaths are on a scale of 0 to 10
    return $ra + $k * ($sa - $ea) + $l * ($pa / ($pa + $pb)) + $sa * $v + $performance_factor;
}

// Simulate a game and update player data
function simulate_game($team1, $team2, $team1_average_elo, $team2_average_elo) {
    // Determine the match result probabilistically
    $probability_team1_win = calculate_expected_outcome($team1_average_elo, $team2_average_elo);
    $match_result = random_int(0, 1000) / 1000; // Generate a random number between 0 and 1
    if ($match_result < $probability_team1_win) {
        $match_result = 1; // Team 1 wins
    } elseif ($match_result > $probability_team1_win) {
        $match_result = 0; // Team 2 wins
    } else {
        $match_result = 0.5; // Draw
    }

    // Update Elo ratings based on match result
    foreach ($team1 as &$player) {
        $player['elo_rating'] = update_elo_with_performance($player['elo_rating'], $match_result, 1, $team1_average_elo, $team2_average_elo, $player['kills'], $player['deaths']);
    }
    foreach ($team2 as &$player) {
        $player['elo_rating'] = update_elo_with_performance($player['elo_rating'], 1 - $match_result, 1, $team2_average_elo, $team1_average_elo, $player['kills'], $player['deaths']);
    }

    // Return updated team elo
    $team1_elo = array_sum(array_column($team1, 'elo_rating')) / count($team1);
    $team2_elo = array_sum(array_column($team2, 'elo_rating')) / count($team2);

    return [$team1_elo, $team2_elo];
}

// Main function
function run($room_num) {
    // Load players
    list($players, $team_elo) = load_players_sql($room_num);
    list($opposite_player, $opposite_team_elo, $opposite_team) = load_opposite_sql($team_elo, $room_num);

    // Simulate a game and update player data
    list($new_team_elo, $new_opposite_team_elo) = simulate_game($players, $opposite_player, $team_elo, $opposite_team_elo);

    // Save updated player data to JSON file
    $all_players = array_merge($players, $opposite_player);
    $teams = [
        ['room_num' => (int)$room_num, 'team_elo' => $new_team_elo],
        ['room_num' => $opposite_team, 'team_elo' => $new_opposite_team_elo]
    ];

    save_players_sql($all_players, $teams);
    return $opposite_team;
}