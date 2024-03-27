import json
import random

class EloRatingSystem:
    def __init__(self, players, K=32, c=400, RA0=1500, Rmin=1000, V=10):
        self.players = players
        self.K = K
        self.c = c
        self.RA0 = RA0
        self.Rmin = Rmin
        self.V = V

    def calculate_expected_score(self, RA, RB):
        QA = 10**(RA/self.c)
        QB = 10**(RB/self.c)
        EA = QA / (QA + QB)
        return EA

    def update_elo_rating(self, player, SA, EA, PA, PB):
        RA = player['elo_rating']
        delta_R = self.K * (SA - EA) + self.V * PA / (PA + PB)
        new_RA = RA + delta_R
        if new_RA < self.Rmin:
            new_RA = self.Rmin
        player['elo_rating'] = new_RA

    def simulate_match(self, team1, team2):
        total_score_team1 = sum(player['kills'] for player in team1)
        total_score_team2 = sum(player['kills'] for player in team2)

        total_combat_score_team1 = sum(player['combat_score'] for player in team1)
        total_combat_score_team2 = sum(player['combat_score'] for player in team2)

        EA_team1 = self.calculate_expected_score(total_combat_score_team1, total_combat_score_team2)
        print("EA of team1 is:", EA_team1)
        EA_team2 = self.calculate_expected_score(total_combat_score_team2, total_combat_score_team1)

        match_result_team1 = random.choice([1, 0.5, 0])
        match_result_team2 = 0

        if(match_result_team1 == 1):
            match_result_team2 = 0
            
        elif(match_result_team1 == 0):
            match_result_team2 = 1
        else:
            match_result_team2 = 0.5

        print(match_result_team1, match_result_team2)

        for player in team1:
            self.update_elo_rating(player, match_result_team1, EA_team1, total_score_team1, total_score_team2)

        for player in team2:
            self.update_elo_rating(player, match_result_team2, EA_team2, total_score_team2, total_score_team1)

        self.update_json_file()

    def print_current_ratings(self):
        for player in self.players:
            print(f"{player['name']}: {player['elo_rating']}")

    def update_json_file(self):
        with open('players.json', 'w') as f:
            json.dump({'players': self.players}, f, indent=4)


if __name__ == "__main__":
    with open('players.json') as f:
        data = json.load(f)
        players = data['players']

    elo_system = EloRatingSystem(players)

    # Simulate a match
    team1 = players[:5]
    team2 = players[5:]

    for i in range(100):
        elo_system.simulate_match(team1, team2)

        # Print updated ratings
        elo_system.print_current_ratings()

    

