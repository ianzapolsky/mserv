<?php

  function render() {

    /*
     * initialize mysql connection
     */
    $conn = new mysqli(
      getenv("AWS_MYSQL_DNS"),
      getenv("AWS_MYSQLUSER"),
      getenv("AWS_MYSQLPASS"),
      "music"
    );
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $sql = 'SELECT distinct artist FROM song ORDER BY artist';

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        echo "<li><a href=\"/test.php?artist=".$row["artist"]."\">" . $row["artist"] . "</a></li>";
      }
    } else {
      echo "<li>0 results</li>";
    }

    $conn->close();

  }

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Zapolsky Music Library</title>
    <meta content="width=device-width, initial-scale=0.6" name="viewport">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">

    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
  </head>

  <body>
    <div id="wrapper">
      <h1>Zapolsky Music Library</h1>
      <audio preload></audio>
      <ol>
        <?php
          render();
        ?>
      </ol>
    </div>
  </body>
</html>
