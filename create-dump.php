<?php
//
// SET UTF
//
header('Content-Type: text/html; charset=utf-8');

//
// INSERT MODELS
//
require_once 'WpProQuiz_Model_Model.php';
require_once 'WpProQuiz_Model_AnswerTypes.php';

//
// DB CONNECTION
//
$host = 'INSERT-HOST';
$dbname = 'INSERT-DB-NAME';
$username = 'INSERT-USER-NAME';
$password = 'INSERT-PASSWORD';
$wp_table_prefix = 'INSERT-PREFIX';

//
// Check whether variables are preset with 'INSERT-'. If so -> die()
//
if ($host === 'INSERT-HOST' || $dbname === 'INSERT-DB-NAME' || $username === 'INSERT-USER-NAME' || $password === 'INSERT-PASSWORD' || $wp_table_prefix === 'INSERT-PREFIX') {
    die("Error: Please adjust the database configuration in your script.");
}

//
// WORKING
//
try {
  $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare("SELECT id, title, question, correct_msg, incorrect_msg, answer_type, answer_data FROM ".$wp_table_prefix."_wp_pro_quiz_question");
  $stmt->execute();

  $questionsArray = [];
  while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data = unserialize($row['answer_data']);
    if ($data === false && $row['answer_data'] !== 'b:0;') {
      throw new Exception('Deserialization failed.');
    }

   $answers = [];
    foreach ($data as $answerType) {
      $answers[] = [
        'answer' => $answerType->getAnswer(),
        'html' => $answerType->isHtml(),
        'points' => $answerType->getPoints(),
        'correct' => $answerType->isCorrect(),
        'sortString' => $answerType->getSortString(),
        'sortStringHtml' => $answerType->isSortStringHtml(),
      ];
    }

      $questionsArray[] = [
        'id' => $row['id'],
        'title' => $row['title'],
        'question' => $row['question'],
        'correct_msg' => $row['correct_msg'],
        'incorrect_msg' => $row['incorrect_msg'],
        'answer_type' => $row['answer_type'],
        'answers' => $answers,
      ];
  } #End While

  $json = json_encode($questionsArray, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

  $sqlFileName = "export_questions.sql";
  $sqlFile = fopen($sqlFileName, 'w') or die("Can't open file!");

  $sqlCreateTable = "CREATE TABLE IF NOT EXISTS `exported_questions` (
  `id` INT NOT NULL,
  `title` VARCHAR(200),
  `question` TEXT,
  `correct_msg` TEXT,
  `incorrect_msg` TEXT,
  `answer_type` VARCHAR(50),
  `answers` JSON,
  `category_id` INT,
  PRIMARY KEY (`id`)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

  fwrite($sqlFile, $sqlCreateTable);

    foreach ($questionsArray as $q) {
      $answersJson = json_encode($q['answers'], JSON_UNESCAPED_UNICODE);
      $answersJsonEscaped = addslashes($answersJson);
      $sqlInsert = sprintf(
      "INSERT INTO `exported_questions` (`id`, `title`, `question`, `correct_msg`, `incorrect_msg`, `answer_type`, `answers`, `category_id`) VALUES (%d, '%s', '%s', '%s', '%s', '%s', '%s', %d);\n",
      $q['id'],
      addslashes($q['title']),
      addslashes($q['question']),
      addslashes($q['correct_msg']),
      addslashes($q['incorrect_msg']),
      addslashes($q['answer_type']),
      $answersJsonEscaped,
      $q['category_id']
      );
      fwrite($sqlFile, $sqlInsert);
    }
  fclose($sqlFile);

  echo "SQL file generated successfully.";

} catch (PDOException $e) {
  die("Database error: " . $e->getMessage());
} catch (Exception $e) {
  die("Error: " . $e->getMessage());
}
