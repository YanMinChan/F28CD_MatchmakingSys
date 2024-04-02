import json
import os
import math
import random
import sys
import mysql.connector
os.chdir('../ES')

# We are going to select 2 teams with closest team elo and match them
# Input: room number
# Action: generate score and store them in sql
# Output: game result(not implemented yet) and opposite team room number

# Load player data from Database 
def load_players_sql(conf, room_num):
    try:
        # Connect to the database
        conn = mysql.connector.connect(**conf)
        cursor = conn.cursor()

        # Extract player list
        cursor.execute("SELECT * FROM players p JOIN player_joinroom pj ON p.name = pj.pname WHERE pj.room_num="+str(room_num))
        rows = cursor.fetchall()

        # Process rows and store into dictionary
        data = []
        for row in rows:
            player = {
                'name': row[0],
                'combat_score': row[3],
                'elo_rating': float(row[4]),
                'kills': row[5],
                'deaths': row[6],
                'room_num': row[7]
            }
            data.append(player) 

        # Extract team elo
        cursor.execute("SELECT team_elo FROM rooms WHERE room_num="+str(room_num))
        team_elo = float(cursor.fetchone()[0])

        # Set in game to true
        cursor.execute("UPDATE rooms SET in_game=true WHERE room_num="+str(room_num))
        
    except mysql.connector.Error as err:
        print("Error:", err)

    finally:
        if conn.is_connected():
            conn.close()
        
    return data, team_elo

# Find a suitable opposite team
def load_opposite_sql(conf, team_elo, room_num):
    try:
        # Connect to the database
        conn = mysql.connector.connect(**conf)
        cursor = conn.cursor()

        # Find a suitable team
        cursor.execute("SELECT room_num FROM rooms WHERE room_num != "+str(room_num)+" AND in_game = false AND player_count = 5 ORDER BY ABS(team_elo-"+str(team_elo)+") LIMIT 1")
        row = cursor.fetchone()
        if (row == None):
            return None

        opposite_team = row[0]

        # Fetch opposite team data
        opposite_data, opposite_team_elo = load_players_sql(conf, opposite_team)
   
    except mysql.connector.Error as err:
        print("Error:", err)

    finally:
        if conn.is_connected():
            conn.close()
    
    return opposite_data, opposite_team_elo, opposite_team

# Save player data to database
def save_players_sql(conf, players, teams):
    try:
        # Connect to the database
        conn = mysql.connector.connect(**conf)
        cursor = conn.cursor()

        # Update query
        for player in players:
            update_query = "UPDATE players SET elo_rating = %s WHERE name = %s"
            new_elo = player['elo_rating']
            player_name = player['name']
            cursor.execute(update_query, (new_elo, player_name))

        # Commit change to database
        conn.commit()

        # Update team elo
        for team in teams:
            update_query = "UPDATE rooms SET team_elo = %s WHERE room_num = %s"
            new_team_elo = team['team_elo']
            room_num = team['room_num']
            cursor.execute(update_query, (new_team_elo, room_num))

        # Commit change to database
        conn.commit()
        
    except mysql.connector.Error as err:
        print("Error:", err)

    finally:
        if conn.is_connected():
            conn.close()

# Calculate expected outcome of a match
def calculate_expected_outcome(ra, rb, c=400):
    qa = 10**(ra/c)
    qb = 10**(rb/c)
    return qa / (qa + qb)

# Update Elo rating after a match considering individual performance
def update_elo_with_performance(ra, sa, ea, pa, pb, kills, deaths, k=32, l=1, v=5):
    performance_factor = (kills - deaths) / 10.0  # Assuming kills and deaths are on a scale of 0 to 10
    return ra + k * (sa - ea) + l * (pa / (pa + pb)) + sa * v + performance_factor

# Simulate a game and update player data
def simulate_game(team1, team2, team1_average_elo, team2_average_elo):
    
    # Determine the match result probabilistically
    probability_team1_win = calculate_expected_outcome(team1_average_elo, team2_average_elo)
    match_result = random.choices([1, 0.5, 0], weights=[probability_team1_win, 1 - probability_team1_win, 1 - probability_team1_win], k=1)[0]
    
    # Update Elo ratings based on match result
    for player in team1:
        player['elo_rating'] = update_elo_with_performance(player['elo_rating'], match_result, 1, team1_average_elo, team2_average_elo, player['kills'], player['deaths'])
    for player in team2:
        player['elo_rating'] = update_elo_with_performance(player['elo_rating'], 1 - match_result, 1, team2_average_elo, team1_average_elo, player['kills'], player['deaths'])

    # # Print updated Elo ratings for each player
    # print("Updated Elo ratings for Team 1:")
    # for player in team1:
    #     print(player['name'], "-", player['elo_rating'])
    
    # print("\nUpdated Elo ratings for Team 2:")
    # for player in team2:
    #     print(player['name'], "-", player['elo_rating'])

    # Return updated team elo
    team1_elo = sum(player['elo_rating'] for player in team1) / 5
    team2_elo = sum(player['elo_rating'] for player in team2) / 5

    return team1_elo, team2_elo

# Main function
def main():
    # command line arg input
    room_num = sys.argv[1]
    
    # Config database details
    config = {
        'host': '132.145.18.222',
        'user': 'yc89',
        'password': 't2!BgOChrfZ',
        'database': 'yc89'
    }

    # Load players
    players, team_elo = load_players_sql(config, room_num)
    opposite_player, opposite_team_elo , opposite_team = load_opposite_sql(config, team_elo, room_num)
    
    # Simulate a game and update player data
    new_team_elo, new_opposite_team_elo = simulate_game(players, opposite_player, team_elo, opposite_team_elo)
    
    # Save updated player data to JSON file
    all_players = players + opposite_player
    teams = []
    team1 = {'room_num': int(room_num), 'team_elo': new_team_elo}
    team2 = {'room_num': opposite_team, 'team_elo': new_opposite_team_elo}
    teams.append(team1)
    teams.append(team2)

    save_players_sql(config, all_players, teams)
    print(opposite_team)

if __name__ == "__main__":
    main()
