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
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO's error mode to Exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Select all questions
    $stmt = $pdo->query("SELECT id, answers FROM exported_questions");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $questionId = $row['id'];
        $answers = json_decode($row['answers'], true);

        foreach ($answers as $index => $answer) {
            // Add a unique ID to each answer
            $newAnswer = [
                'id' => $index + 1, // Start the ID at 1
                'answer' => $answer['answer'],
                'correct' => $answer['correct'],
                'points' => $answer['points'],
            ];
            
            // Replace the old answer with the newly formatted answer
            $answers[$index] = $newAnswer;
        }

        // Update the question with the new answers
        $updatedAnswers = json_encode($answers);
        $updateStmt = $pdo->prepare("UPDATE exported_questions SET answers = :answers WHERE id = :id");
        $updateStmt->execute([':answers' => $updatedAnswers, ':id' => $questionId]);
    }

    echo "All questions have been updated successfully.";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
} catch (Exception $e) {
    die("General error: " . $e->getMessage());
}
?>