<?php
include 'db.php';

function delete_from_favorites($conn, $word) {
    // Prepare the SQL statement with a placeholder for the word
    $query = "DELETE FROM my_favorites WHERE word = $1";

    // Execute the query with the word parameter
    $result = pg_query_params($conn, $query, array($word));

    // Check if the query was successful
    if ($result) {
        return ["success" => "Record deleted successfully."];
    } else {
        return ["error" => pg_last_error($conn)];
    }
}

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);

    // Logging the incoming data for debugging
    file_put_contents('log.txt', print_r($input, true), FILE_APPEND);

    if (isset($input['word'])) {
        $response = delete_from_favorites($conn, $input['word']);
        echo json_encode($response);
    } else {
        echo json_encode(["error" => "Invalid input"]);
    }
    exit;
}
?>
