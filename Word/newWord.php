<?php
include 'db.php';

function fetch_new_word($ref, $key) {
    $uri = "https://dictionaryapi.com/api/v3/references/" . urlencode($ref) . "/json/random?key=" . urlencode($key);
    $response = @file_get_contents($uri);

    if ($response === FALSE) {
        return ["error" => "Unable to fetch data from API."];
    }

    $data = json_decode($response, true);

    if (isset($data[0]['meta']['id'], $data[0]['shortdef'][0])) {
        return [
            "word" => $data[0]['meta']['id'],
            "definition" => $data[0]['shortdef'][0]
        ];
    } else {
        return ["error" => "No definition found"];
    }
}

function save_to_favorites($conn, $word, $definition) {
    // Prepare the SQL statement with placeholders
    $query = "INSERT INTO my_favorites (word, definition) VALUES ($1, $2)";

    // Execute the query with parameters
    $result = pg_query_params($conn, $query, array($word, $definition));

    // Check if the query was successful
    if ($result) {
        echo "Record inserted successfully.";
    } else {
        echo "Error: " . pg_last_error($conn);
    }
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Logging the incoming data for debugging
    file_put_contents('log.txt', print_r($input, true), FILE_APPEND);

    if (isset($input['like'], $input['word'], $input['definition'])) {
        save_to_favorites($conn, $input['word'], $input['definition']);
        echo json_encode(["success" => "Word added to favorites"]);
    } else {
        echo json_encode(["error" => "Invalid input"]);
    }
    exit;
}

session_start();
if (!isset($_SESSION['last_fetched']) || time() - $_SESSION['last_fetched'] > 86400) {
    $word_data = fetch_new_word("learners", "c49e65a6-53cd-4c9b-b260-999022a132d9");
    if (!isset($word_data['error'])) {
        $_SESSION['word'] = $word_data['word'];
        $_SESSION['definition'] = $word_data['definition'];
        $_SESSION['last_fetched'] = time();
    }
}

if (isset($_SESSION['word'], $_SESSION['definition'])) {
    echo json_encode([
        "word" => $_SESSION['word'],
        "definition" => $_SESSION['definition']
    ]);
} else {
    echo json_encode(["error" => "Unable to fetch new word"]);
}
?>
