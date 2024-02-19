import mysql.connector

# Replace with your actual database credentials
config = {
    'host': '132.145.18.222',
    'user': 'yc89',
    'password': 't2!BgOChrfZ',
    'database': 'yc89'
}

try:
    # Connect to the database
    conn = mysql.connector.connect(**config)
    cursor = conn.cursor()

    # Assuming a table named 'players' with columns 'id', 'name', 'level', etc.
    sql1 = "CREATE TABLE players (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), elo INT)"
    sql2 = "INSERT INTO players (id, name, elo) VALUES (%s, %s, %s)"
    val = (1, "Peter", 1200)  # Example player data

    cursor.execute(sql1)
    cursor.execute(sql2, val)
    conn.commit()

    print(cursor.rowcount, "record inserted.")

except mysql.connector.Error as err:
    print("Error:", err)
finally:
    if conn.is_connected():
        conn.close()
        print("Connection closed.")