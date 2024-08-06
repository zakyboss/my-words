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

        input {
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            width: calc(100% - 24px);
        }

        button {
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            background-color: #28a745;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
        }

        button:hover {
            background-color: #218838;
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

        .nav-button {
            background-color: #007bff;
            margin-top: 20px;
        }

        .nav-button:hover {
            background-color: #0056b3;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Word Manipulation App</title>
</head>
<body>
    <div class="container">
        <h1>Word Manipulation App</h1>
        <input type="text" id="inputWord" placeholder="Enter a word">
        <button onclick="processWord()">Process</button>
        <div class="results">
            <p>Reversed: <span id="reversedWord"></span></p>
            <p>Definition: <span id="definitionWord"></span></p>
            <p>Plural: <span id="pluralWord"></span></p>
            <button id="likeButton" style="display:none;" onclick="likeWord()">Like</button>
        </div>
        <button class="nav-button" onclick="location.href='myFavorites.php'">View Favorites</button>
    </div>
    <script>
        function processWord() {
            const inputWord = document.getElementById('inputWord').value;

            if (inputWord) {
                document.getElementById('reversedWord').innerText = reverseWord(inputWord);
                fetchDefinition(inputWord).then(definition => {
                    document.getElementById('definitionWord').innerText = definition;
                });
                document.getElementById('pluralWord').innerText = convertToPlural(inputWord);
            } else {
                alert('Please enter a word');
            }
        }

        function reverseWord(word) {
            return word.split('').reverse().join('');
        }

        async function fetchDefinition(word) {
            const response = await fetch(`definition.php?word=${word}`);
            const data = await response.json();
            return data.definition || 'No definition found';
        }

        function convertToPlural(word) {
            if (word.endsWith('y')) {
                return word.slice(0, -1) + 'ies';
            } else if (word.endsWith('s') || word.endsWith('sh') || word.endsWith('ch') || word.endsWith('x') || word.endsWith('z')) {
                return word + 'es';
            } else {
                return word + 's';
            }
        }

        async function loadNewWord() {
            const response = await fetch('newWord.php');
            const data = await response.json();
            if (!data.error) {
                document.getElementById('inputWord').value = data.word;
                document.getElementById('definitionWord').innerText = data.definition;
                document.getElementById('likeButton').style.display = 'inline-block';
            } else {
                alert(data.error);
            }
        }

        async function likeWord() {
            const word = document.getElementById('inputWord').value;
            const definition = document.getElementById('definitionWord').innerText;

            const response = await fetch('newWord.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ like: true, word: word, definition: definition })
            });
            const data = await response.json();
            if (data.success) {
                alert('Word added to favorites');
            } else {
                alert(data.error);
            }
        }

        // Load a new word on page load
        window.onload = loadNewWord;
    </script>
</body>
</html>
