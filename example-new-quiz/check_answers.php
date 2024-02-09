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

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['answers'])) {
    $userAnswers = $_POST['answers'];
    
    $results = [];
    $totalScore = 0;

    foreach ($userAnswers as $questionId => $submittedAnswers) {
        if (!is_array($submittedAnswers)) {
            $submittedAnswers = [$submittedAnswers];
        }

        try {
            $stmt = $pdo->prepare("SELECT question, answers, incorrect_msg FROM exported_questions WHERE id = :id");
            $stmt->bindParam(':id', $questionId, PDO::PARAM_INT);
            $stmt->execute();
            $questionData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $correctAnswers = json_decode($questionData['answers'], true);
            $questionText = $questionData['question'];
            $formattedAnswers = [];
            $score = 0;
            $incorrect = false;

            foreach ($correctAnswers as $correctAnswer) {
                $isSelected = in_array($correctAnswer['answer'], $submittedAnswers);
                $isCorrect = $correctAnswer['correct'];
                if ($isSelected && $isCorrect) {
                    $score += $correctAnswer['points'];
                    $formattedAnswers[] = "<font color=\"green\">{$correctAnswer['answer']}</font>";
                } elseif ($isSelected && !$isCorrect) {
                    $incorrect = true;
                    $formattedAnswers[] = "<font color=\"red\">{$correctAnswer['answer']}</font>";
                } else {
                    $formattedAnswers[] = $correctAnswer['answer'];
                }
            }

            $correctCount = count(array_filter($correctAnswers, function ($answer) { return $answer['correct']; }));
            $selectedCorrectCount = count(array_intersect(array_column($correctAnswers, 'answer'), $submittedAnswers));
            if ($incorrect || $selectedCorrectCount < $correctCount) {
                $results[$questionId] = [
                    'question' => "&#10008; <b>Frage:</b> <font color=\"red\">$questionText</font>",
                    'answers' => $formattedAnswers,
                    'score' => 0,
                    'message' => $questionData['incorrect_msg']
                ];
            } else {
                $results[$questionId] = [
                    'question' => "&#10004; <b>Frage:</b> <font color=\"green\">$questionText</font>",
                    'answers' => $formattedAnswers,
                    'score' => $score,
                    'message' => ''
                ];
                $totalScore += $score;
            }

        } catch (PDOException $e) {
            die("Datenbankfehler: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Quiz Auswertung</title>
    <style>
        .correct { color: green; }
        .incorrect { color: red; }
        .question, .answer { margin-bottom: 10px; }
    </style>
</head>
<body>
    <h1>Quiz Auswertung</h1>

    <?php if (!empty($results)): ?>
        <div>
            <?php foreach ($results as $questionId => $result): ?>
                <div class="question">
                    <?php echo $result['question']; ?>
                </div>
                <div class="answers">
                    <?php foreach ($result['answers'] as $answer): ?>
                        <div class="answer"><li><?php echo $answer;  ?></li></div>
                    <?php endforeach; ?>
                    <?php if (!empty($result['message'])): ?>
                        <div class="feedback"><b>Warum ist das falsch?</b> <?php echo $result['message']; ?></div>
                    <?php endif; ?>
                </div>
               <br>
               <hr>
               <br>
            <?php endforeach; ?>
            <div class="total-score">
                <strong>Gesamtpunktzahl: <?php echo $totalScore; ?></strong>
            </div>
        </div>
    <?php else: ?>
        <p>Es liegen keine Ergebnisse vor.</p>
    <?php endif; ?>
</body>
</html>


