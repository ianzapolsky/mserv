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
    <title>audio.js</title>
    <meta content="width=device-width, initial-scale=0.6" name="viewport">
    <style>
      body { color: #666; font-family: sans-serif; line-height: 1.4; }
      h1 { color: #444; font-size: 1.2em; padding: 14px 2px 12px; margin: 0px; }
      h1 em { font-style: normal; color: #999; }
      a { color: #888; text-decoration: none; }
      #wrapper { width: 400px; margin: 40px auto; }

      ol { padding: 0px; margin: 0px; list-style: decimal-leading-zero inside; color: #ccc; width: 460px; border-top: 1px solid #ccc; font-size: 0.9em; }
      ol li { position: relative; margin: 0px; padding: 9px 2px 10px; border-bottom: 1px solid #ccc; cursor: pointer; }
      ol li a { display: block; text-indent: -3.3ex; padding: 0px 0px 0px 20px; }
      li.playing { color: #aaa; text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.3); }
      li.playing a { color: #000; }
      li.playing:before { content: '♬'; width: 14px; height: 14px; padding: 3px; line-height: 14px; margin: 0px; position: absolute; left: -24px; top: 9px; color: #000; font-size: 13px; text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.2); }

      #shortcuts { position: fixed; bottom: 0px; width: 100%; color: #666; font-size: 0.9em; margin: 60px 0px 0px; padding: 20px 20px 15px; background: #f3f3f3; background: rgba(240, 240, 240, 0.7); }
      #shortcuts div { width: 460px; margin: 0px auto; }
      #shortcuts h1 { margin: 0px 0px 6px; }
      #shortcuts p { margin: 0px 0px 18px; }
      #shortcuts em { font-style: normal; background: #d3d3d3; padding: 3px 9px; position: relative; left: -3px;
        -webkit-border-radius: 4px; -moz-border-radius: 4px; -o-border-radius: 4px; border-radius: 4px;
        -webkit-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); -moz-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); -o-box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); box-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1); }

      @media screen and (max-device-width: 480px) {
        #wrapper { position: relative; left: -3%; }
        #shortcuts { display: none; }
      }
    </style>
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
        })
      });
    </script>
  </head>
  <body>
    <div id="wrapper">
      <h1>Ian's Music</h1>
      <audio preload></audio>
      <ol>
        <?php
          get_music();
        ?>
      </ol>
    </div>
  </body>
</html>