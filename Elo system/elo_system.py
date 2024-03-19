import json
import math
import random

# Load player data from JSON file
def load_players(filename):
    with open(filename, 'r') as file:
        data = json.load(file)
        return data['players']

# Save player data to JSON file
def save_players(filename, players):
    with open(filename, 'w') as file:
        json.dump({"players": players}, file, indent=4)

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
def simulate_game(players):
    # Example match between two teams (5v5)
    team1 = players[:5]
    team2 = players[5:]
    
    # Calculate average Elo rating for each team
    team1_average_elo = sum(player['elo_rating'] for player in team1) / 5
    team2_average_elo = sum(player['elo_rating'] for player in team2) / 5
    
    # Determine the match result probabilistically
    probability_team1_win = calculate_expected_outcome(team1_average_elo, team2_average_elo)
    match_result = random.choices([1, 0.5, 0], weights=[probability_team1_win, 1 - probability_team1_win, 1 - probability_team1_win], k=1)[0]
    
    # Update Elo ratings based on match result
    for player in team1:
        player['elo_rating'] = update_elo_with_performance(player['elo_rating'], match_result, 1, team1_average_elo, team2_average_elo, player['kills'], player['deaths'])
    for player in team2:
        player['elo_rating'] = update_elo_with_performance(player['elo_rating'], 1 - match_result, 1, team2_average_elo, team1_average_elo, player['kills'], player['deaths'])

    # Print updated Elo ratings for each player
    print("Updated Elo ratings for Team 1:")
    for player in team1:
        print(player['name'], "-", player['elo_rating'])
    
    print("\nUpdated Elo ratings for Team 2:")
    for player in team2:
        print(player['name'], "-", player['elo_rating'])


# Main function
def main():
    players = load_players("players.json")
    
    # Simulate a game and update player data
    simulate_game(players)
    
    # Save updated player data to JSON file
    save_players("players.json", players)

if __name__ == "__main__":
    main()
