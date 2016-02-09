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

<body>
  <div>
    <ul>
      <?php
        render();
      ?>
    </ul>
  </div>
</body>
