<!-- Write a PHP program to accept a sentence from the user, convert the sentence into an array
of words, and use appropriate PHP string and array functions to count the total number of
words and determine the frequency of each word. Display each word along with its
frequency. -->

<?php
$sentence = "This is a sample sentence. This sentence is for testing purposes.";
// Convert the sentence into an array of words
$words = str_word_count($sentence,1);

// count the total number of words
$total_words = count($words);
echo "Total number of words: " . $total_words . "<br>";

// determine the frequency of each word
$word_frequency = array_count_values($words);
// display each word along with its frequency
foreach($word_frequency as $word => $frequency){
    echo "Word: '$word' - Frequency: $frequency<br>";
}
