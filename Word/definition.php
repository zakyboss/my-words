<?php

function grab_json_definition($word, $ref, $key) {
    $uri = "https://dictionaryapi.com/api/v3/references/" . urlencode($ref) . "/json/" . urlencode($word) . "?key=" . urlencode($key);
    $response = @file_get_contents($uri);

    if ($response === FALSE) {
        return json_encode(["error" => "Unable to fetch data from API."]);
    }

    $data = json_decode($response, true);

    if (isset($data[0]['shortdef'][0])) {
        return json_encode(["definition" => $data[0]['shortdef'][0]]);
    } else {
        return json_encode(["definition" => "No definition found"]);
    }
}

if (isset($_GET['word'])) {
    $word = $_GET['word'];
    $definition = grab_json_definition($word, "learners", "c49e65a6-53cd-4c9b-b260-999022a132d9");
    echo $definition;
} else {
    echo json_encode(["error" => "No word provided"]);
}

?>
