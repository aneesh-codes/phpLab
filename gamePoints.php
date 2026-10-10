<!DOCTYPE html>
<html>
<head>
  <title>Football Points</title>
</head>
<body>
  <h2>Football Team Points</h2>

  <form method="post" action="">
    Wins: <input type="number" name="wins"><br><br>
    Draws: <input type="number" name="draws"><br><br>
    Losses: <input type="number" name="losses"><br><br>
    <input type="submit" name="submit" value="Calculate">
  </form>

  <?php
  // Function to calculate total points: 3 per win, 1 per draw, 0 per loss
  function TotalPoints($wins, $draws, $losses){
    $totalPoints = ($wins * 3) + ($draws * 1) + ($losses * 0);
    return $totalPoints;
  }

  // accepting the values through html form
  if(isset($_POST['submit'])){
    $wins = $_POST['wins'];
    $draws = $_POST['draws'];
    $losses = $_POST['losses'];

    // validation: all fields filled, whole numbers only, no negatives
    if(!ctype_digit($wins) || !ctype_digit($draws) || !ctype_digit($losses)){
      echo "Please enter non-negative whole numbers in all fields.";
    } else {
      // function call
      $totalPoints = TotalPoints($wins, $draws, $losses);

      // displaying total games played and total points
      $totalGames = $wins + $draws + $losses;
      echo "Total Games Played: " . $totalGames . "<br>";
      echo "Total Points: " . $totalPoints;
    }
  }
  ?>
</body>
</html>