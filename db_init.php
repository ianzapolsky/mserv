<?php

  /*
   * initialize mysql connection
   */
  $conn = new mysqli(
    getenv("AWS_MYSQL_DNS"),
    getenv("AWS_MYSQLUSER"),
    getenv("AWS_MYSQLPASS")
  );
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  /*
   * create music database, and song table within
   */
  $sql = "CREATE DATABASE music";
  if ($conn->query($sql) !== true) {
    die("Error creating database: " . $conn->error);
  }
  $sql = "USE music";
  if ($conn->query($sql) !== true) {
    die("Error switching to database: " . $conn->error);
  }
  $sql = "CREATE TABLE song (
    id           INT(6) AUTO_INCREMENT PRIMARY KEY,
    title        VARCHAR(200),
    artist       VARCHAR(200),
    album        VARCHAR(200),
    album_artist VARCHAR(200),
    genre        VARCHAR(100),
    track        INT(6),
    url          VARCHAR(500)
  )";
  if ($conn->query($sql) !== true) {
    die("Error creating song table: " . $conn->error);
  }

?>
