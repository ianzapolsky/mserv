<?php

  function get_music() {

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

    $page_size = 100;

    $sql = 'SELECT * FROM song
      WHERE
        id IS NOT NULL'
        .(isset($_GET['title']) ? ' AND title LIKE \'%'.mysql_escape_string($_GET['title']).'%\'' : '')
        .(isset($_GET['artist']) ? ' AND artist LIKE \'%'.mysql_escape_string($_GET['artist']).'%\'' : '')
        .(isset($_GET['album']) ? ' AND album LIKE \'%'.mysql_escape_string($_GET['album']).'%\'' : '')
        .(isset($_GET['albumartist']) ? ' AND albumartist LIKE \'%'.mysql_escape_string($_GET['albumartist']).'%\'' : '')
        .(isset($_GET['genre']) ? ' AND genre LIKE \'%'.mysql_escape_string($_GET['genre']).'%\'' : '')
      .(isset($_GET['random']) ? ' ORDER BY RAND()' : ' ORDER BY album, track')
      .' LIMIT ' . $page_size;

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      // output data of each row
      while($row = $result->fetch_assoc()) {
        echo "<li><a href=\"#\" data-src=\"". $row["url"] . "\">[Track " . $row["track"] . "] <i>". $row["title"] . "</i> by <strong>" . $row["artist"] ."</strong> from <strong>" . $row["album"] . "</strong></a></li>";
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
    <link rel="stylesheet" href="/css/main.css">

    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="/audiojs/audio.min.js"></script>
    <script>
      $(function() { 
        // Setup the player to autoplay the next track
        var a = audiojs.createAll({
          trackEnded: function() {
            var next = $('ol li.playing').next();
            if (!next.length) next = $('ol li').first();
            next.addClass('playing').siblings().removeClass('playing');
            audio.load($('a', next).attr('data-src'));
            audio.play();
          }
        });

        // Load in the first track
        var audio = a[0];
            first = $('ol a').attr('data-src');
        $('ol li').first().addClass('playing');
        audio.load(first);

        // Load in a track on click
        $('ol li').click(function(e) {
          e.preventDefault();
          $(this).addClass('playing').siblings().removeClass('playing');
          audio.load($('a', this).attr('data-src'));
          audio.play();
        });
        // Keyboard shortcuts
        $(document).keydown(function(e) {
          var unicode = e.charCode ? e.charCode : e.keyCode;
             // right arrow
          if (unicode == 39) {
            var next = $('li.playing').next();
            if (!next.length) next = $('ol li').first();
            next.click();
            // back arrow
          } else if (unicode == 37) {
            var prev = $('li.playing').prev();
            if (!prev.length) prev = $('ol li').last();
            prev.click();
            // spacebar
          } else if (unicode == 32) {
            audio.playPause();
          }
          return false;
        })
      });
    </script>
  </head>
  <body>
    <div id="wrapper">
      <h1>Zapolsky Music Library</h1>
      <audio preload></audio>
      <ol>
        <?php
          get_music();
        ?>
      </ol>
    </div>
  </body>
</html>
