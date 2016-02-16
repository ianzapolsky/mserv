<?php

  function get_music() {

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
        echo "<tr class=\"song\" data-album=\"" . $row["album"] . "\" data-artist=\"" . $row["album_artist"] . "\" data-src=\"" . $row["url"] . "\">
                <td>" . $row["track"] . "</td><td>". $row["title"] . "</td><td>" . $row["artist"] ."</td><td>" . $row["album"] . "</td>
              </tr>";
      }
    } else {
      echo "<tr><td>0 results</tr></td>";
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
    <link rel="stylesheet" href="/css/main.css">

    <script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/jquery.tablesorter.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/jquery.tablesorter.widgets.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.tablesorter/2.21.1/js/widgets/widget-build-table.js"></script>
    <script src="/audiojs/audio.js"></script>

    <script>

      var loadAlbumArt = function(album, artist) {
        console.log('Requesting new album art from Google...');
        $.get('https://www.googleapis.com/customsearch/v1?q='+album+' '+artist+' album art&cx=012813865030616110872:qez66450mmo&imgColorType=color&searchType=image&key=AIzaSyBux1Byau1kSgaHkOTp4vYf97f7HIl1dJw', function(data) {
          if (data.items.length > 0) {
            var link = data.items[0].link;
            var img = document.createElement('img');
            $(img).attr('src', link)
              .attr('width', '300')
              .attr('height', '300');
            $('#album-art-container').html($(img));
          }
        });
      };

      $(document).ready(function() {

        $('table').tablesorter({
          sortList: [[3,0]],
          theme: 'dropbox',
          widgets: ['filter']
        });

        var album = null;
        var artist = null;

        var a = audiojs.createAll({
          trackEnded: function() {
            var next = $('.song.playing').next();
            if (!next.length) next = $('.song').first();
            next.addClass('playing active').siblings().removeClass('playing active');
            audio.load(next.attr('data-src'));
            if (next.attr('data-album') !== album) {
              loadAlbumArt(next.attr('data-album'), next.attr('data-artist'));
            }
            audio.play();
          }
        });
        // Load in the first track
        var audio = a[0];
            first = $('.song').attr('data-src');
        $('.song').first().addClass('playing active');
        album = $('.song').first().attr('data-album');
        artist = $('.song').first().attr('data-artist');
        loadAlbumArt(album, artist);
        audio.load(first);

        // Load in a track on click
        $('.song').click(function(e) {
          e.preventDefault();
          $(this).addClass('playing active').siblings().removeClass('playing active');
          audio.load($(this).attr('data-src'));
          if ($(this).attr('data-album') !== album) {
            loadAlbumArt($(this).attr('data-album'), $(this).attr('data-artist'));
          }
          audio.play();
        });
        // Keyboard shortcuts
        $(document).keydown(function(e) {
          var unicode = e.charCode ? e.charCode : e.keyCode;
             // right arrow
          if (unicode == 39) {
            var next = $('.song.playing').next();
            if (!next.length) next = $('.song').first();
            next.click();
            return false;
            // back arrow
          } else if (unicode == 37) {
            var prev = $('.song.playing').prev();
            if (!prev.length) prev = $('.song').last();
            prev.click();
            return false;
            // spacebar
          } else if (unicode == 32) {
            audio.playPause();
            return false;
          }
        });

      });
    </script>
  </head>
  <body>
    <div class="container">

      <h1><a href="/artists.php">Zapolsky Music Library</a></h1>

      <div class="container col-xs-6">
        <audio preload></audio>
      </div>

      <div id="album-art-container" class="container col-xs-6">
      </div>

      <div class="container col-lg-12 col-xl-12 col-md-12 col-sm-12 col-xs-12">
      <br></br>
        <table class="table table-condensed table-hover tablesorter">
          <thead>
            <tr>
              <td>Track</td><td>Title</td><td>Artist</td><td>Album</td>
            </tr>
          </thead>
          <tbody>
            <?php
              get_music();
            ?>
          </tbody>
        </table
      </div>

    </div>
  </body>
</html>
