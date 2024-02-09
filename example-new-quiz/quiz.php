<?php

//
// DB CONNECTION
//
$host = 'INSERT-HOST';
$dbname = 'INSERT-DB-NAME';
$username = 'INSERT-USER-NAME';
$password = 'INSERT-PASSWORD';

//
// Check whether variables are preset with 'INSERT-'. If so -> die()
//
if ($host === 'INSERT-HOST' || $dbname === 'INSERT-DB-NAME' || $username === 'INSERT-USER-NAME' || $password === 'INSERT-PASSWORD') {
    die("Error: Please adjust the database configuration in your script.");
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Datenbankfehler: " . $e->getMessage());
}

$qcount = isset($_GET['qcount']) ? (int)$_GET['qcount'] : 20; 

$stmt = $pdo->prepare("SELECT * FROM exported_questions ORDER BY RAND() LIMIT :qcount");
$stmt->bindParam(':qcount', $qcount, PDO::PARAM_INT);
$stmt->execute();

$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz</title>
<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        margin: 0;
        padding: 20px;
        color: #333;
    }

    form {
        background-color: #fff;
        padding: 20px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        max-width: 600px;
        margin: 20px auto;
    }

    p {
        line-height: 1.6;
    }

    input[type="radio"], input[type="checkbox"] {
        margin-right: 10px;
    }

    label {
        display: block;
        padding: 5px 0;
    }

    button {
        background-color: #007bff;
        color: #ffffff;
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #0056b3;
    }

    strong {
        color: #007bff;
    }
</style>

</head>
<body>
    <form action="check_answers.php" method="post">
        <?php foreach ($questions as $index => $question): ?>
            <div>
                <p><strong>Frage <?php echo $index + 1; ?>:</strong><br> <?php echo nl2br($question['question']); ?></p>
                <?php
                $answers = json_decode($question['answers'], true);
                shuffle($answers);

                foreach ($answers as $answer):
                    $inputType = $question['answer_type'] == 'single' ? 'radio' : 'checkbox';
                ?>
                    <label>
                        <input type="<?php echo $inputType; ?>" name="answers[<?php echo $question['id']; ?>]<?php echo $inputType == 'checkbox' ? '[]' : ''; ?>" value="<?php echo $answer['answer']; ?>">
                        <?php echo nl2br($answer['answer']); ?>
                    </label><br>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
        <button type="submit">Antworten &Uuml;berpr&uuml;fen</button>
    </form>
</body>
</html>