from http.server import BaseHTTPRequestHandler, HTTPServer
import json

class RequestHandler(BaseHTTPRequestHandler):
    def do_GET(self):
        self.send_response(200)
        self.send_header('Content-type', 'application/json')
        self.end_headers()
        
        # Define a list of player names (replace this with your logic to fetch player names)
        player_names = ["Player1", "Player2", "Player3", "Player4", "Player5"]
        
        # Convert the list to JSON format
        response = json.dumps({"players": player_names})
        
        # Write the JSON response to the client
        self.wfile.write(response.encode())

def run(server_class=HTTPServer, handler_class=RequestHandler, port=8080):
    server_address = ('', port)
    httpd = server_class(server_address, handler_class)
    print('Starting server...')
    httpd.serve_forever()

if __name__ == '__main__':
    run()