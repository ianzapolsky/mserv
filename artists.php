<?php

  function render() {

    /*
     * initialize mysql connection
     */
    $conn = new mysqli(
      getenv("AWS_MYSQL_HOST"),
      getenv("AWS_MYSQL_USERNAME"),
      getenv("AWS_MYSQL_PASSWORD"),
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
        echo "<tr><td><h5><a href=\"/player.php?artist=".$row["artist"]."\">" . $row["artist"] . "</a></h5></td></tr>";
      }
    } else {
      echo "<tr><td>0 results</td></tr>";
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/css/theme.dropbox.css">

    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/jquery.tablesorter.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/jquery.tablesorter.widgets.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/widgets/widget-build-table.js"></script>

    <script>
      $(document).ready(function() {
        $('table').tablesorter({
          sortList: [[0,0]],
          theme: 'dropbox',
          widgets: ['filter']
        });
      });
    </script>
  </head>

  <body>
    <div class="container">
      <h1>Zapolsky Music Library</h1>
      <div class="container">

        <audio preload></audio>
        <table class="table table-hover table-condensed tablesorter">
          <thead>
            <tr>
              <th>Artist</th>
            </tr>
          </thead>
          <tbody>
          <?php
            render();
          ?>
          </tbody>
        </table>

      </div>
    </div>
  </body>
</html>
