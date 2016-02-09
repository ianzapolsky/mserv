<?php

  /*
   * read in arguments
   */
  $title = $argv[1];
  $artist = $argv[2];
  $album = $argv[3];
  $album_artist = $argv[4];
  $genre = $argv[5];
  $track = intval($argv[6]);
  $url = $argv[7];

  foreach($argv as $arg) {
    echo mysql_escape_string($arg) . "\n";
  }

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

  /*
   * insert record into database
   */
  $sql = "INSERT INTO song (
    title,
    artist,
    album,
    album_artist,
    genre,
    track,
    url
  )
  VALUES (
    \"".mysql_escape_string($title)."\",
    \"".mysql_escape_string($artist)."\",
    \"".mysql_escape_string($album)."\",
    \"".mysql_escape_string($album_artist)."\",
    \"".mysql_escape_string($genre)."\",
    ".mysql_escape_string($track).",
    \"".mysql_escape_string($url)."\"
  )";
  if ($conn->query($sql) !== true) {
    die("Error creating song table: " . $conn->error);
  }

?>
