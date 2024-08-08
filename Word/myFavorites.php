<?php
include 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f4f4f4;
        }

        .container {
            text-align: center;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            width: 400px;
        }

        button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .results {
            margin-top: 25px;
            text-align: left;
        }

        .results p {
            margin: 10px 0;
            font-size: 16px;
        }

        .results span {
            display: inline-block;
            background-color: #e9ecef;
            padding: 5px 10px;
            border-radius: 5px;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorite Words</title>
</head>
<body>
    <div class="container">
        <h1>Favorite Words</h1>
        <div class="results">
            <?php
            $result = pg_query($conn, "SELECT word, definition FROM my_favorites ORDER BY created_at DESC");

            if (pg_num_rows($result) > 0) {
                while ($row = pg_fetch_assoc($result)) {
                    echo "<p><strong>{$row['word']}</strong>: <span>{$row['definition']}</span></p>";
                }
            } else {
                echo "<p>No favorites found.</p>";
            }
            ?>
        </div>
        <button onclick="location.href='index.php'">Back to Home</button>
    </div>
</body>
</html>
