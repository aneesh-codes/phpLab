<!-- Write a PHP program that accepts a sentence and student information through an HTML
form and demonstrates appropriate built-in string and array functions. The program should
process the sentence by performing character counting, word counting, case conversion,
reversing, searching, and replacement, and should process the student data by performing
searching, sorting, counting, adding, removing, and calculating total and average marks.
Display all processed results in a clear and properly formatted HTML output. -->

<?php
$sentence = 'This is PHP programming. PHP is a server-side scripting language.';
$search_word = '';
$replace_word = '';
$search_student = '';

$char_count = 0;
$word_count = 0;
$upper_case = '';
$lower_case = '';
$reversed_sentence = '';
$replaced_sentence = '';
$student_found = false;
$student_marks = 0;
$sorted_by_name_asc = [];
$sorted_by_marks_asc = [];
$total_marks = 0;
$average_marks = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sentence = $_POST['sentence'] ?? '';
    $search_word = $_POST['search_word'] ?? '';
    $replace_word = $_POST['replace_word'] ?? '';
    $search_student = $_POST['search_student'] ?? '';

    $char_count = strlen($sentence);
    $word_count = str_word_count($sentence);
    $upper_case = strtoupper($sentence);
    $lower_case = strtolower($sentence);
    $reversed_sentence = strrev($sentence);
    $replaced_sentence = str_replace($search_word, $replace_word, $sentence);

    $students = [
        "ABC" => 85,
        "XYZ" => 75,
        "ABD" => 95,
        "PQR" => 80
    ];

    if (array_key_exists($search_student, $students)) {
        $student_found = true;
        $student_marks = $students[$search_student];
    } else {
        $student_found = false;
        $student_marks = "Not Found";
    }

    $sorted_name = $students;
    asort($sorted_name);
    $sorted_by_name_asc = array_keys($sorted_name);

    $sorted_marks = $students;
    arsort($sorted_marks);
    $sorted_by_marks_asc = array_values($sorted_marks);

    $total_marks = array_sum($students);
    $average_marks = count($students) > 0 ? $total_marks / count($students) : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PHP String and Array Functions</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .box { border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; }
        label { display: inline-block; width: 150px; }
        input, button { margin: 5px 0; padding: 6px; }
    </style>
</head>
<body>
    <h2>Sentence Processing</h2>
    <form method="POST">
        <div class="box">
            <label>Sentence:</label>
            <input type="text" name="sentence" value="<?= htmlspecialchars($sentence) ?>" size="80"><br>

            <label>Search Word:</label>
            <input type="text" name="search_word" value="<?= htmlspecialchars($search_word) ?>"><br>

            <label>Replace Word:</label>
            <input type="text" name="replace_word" value="<?= htmlspecialchars($replace_word) ?>"><br>

            <label>Search Student:</label>
            <input type="text" name="search_student" value="<?= htmlspecialchars($search_student) ?>"><br>

            <button type="submit">Process</button>
        </div>
    </form>

    <?php if ($_SERVER["REQUEST_METHOD"] == "POST") : ?>
        <div class="box">
            <h3>Sentence Results</h3>
            <p><strong>Character Count:</strong> <?= $char_count ?></p>
            <p><strong>Word Count:</strong> <?= $word_count ?></p>
            <p><strong>Uppercase:</strong> <?= htmlspecialchars($upper_case) ?></p>
            <p><strong>Lowercase:</strong> <?= htmlspecialchars($lower_case) ?></p>
            <p><strong>Reversed:</strong> <?= htmlspecialchars($reversed_sentence) ?></p>
            <p><strong>Replaced:</strong> <?= htmlspecialchars($replaced_sentence) ?></p>
        </div>

        <div class="box">
            <h3>Student Results</h3>
            <p><strong>Student Found:</strong> <?= $student_found ? 'Yes' : 'No' ?></p>
            <p><strong>Marks:</strong> <?= htmlspecialchars((string)$student_marks) ?></p>
            <p><strong>Sorted by Name (Ascending):</strong> <?= htmlspecialchars(implode(', ', $sorted_by_name_asc)) ?></p>
            <p><strong>Sorted by Marks (Descending):</strong> <?= htmlspecialchars(implode(', ', $sorted_by_marks_asc)) ?></p>
            <p><strong>Total Marks:</strong> <?= $total_marks ?></p>
            <p><strong>Average Marks:</strong> <?= number_format($average_marks, 2) ?></p>
        </div>
    <?php endif; ?>
</body>
</html>

