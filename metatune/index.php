<?php require_once("lib/metatune/config.php");?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="no-NB">
    <head profile="http://gmpg.org/xfn/11">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="keywords" content="Spotify Metadata API, Mikael Brevik, PHP Lib, Spotify" />
        <meta name="description" content="MetaTune examples" />
        <title>MetaTune - Demo</title>
        <link type="text/css" rel="stylesheet" href="./demo.css" />
    </head>
    <body>
        <div id="wrapper">
            <form action="" method="post">
                <fieldset>
                    <legend>Search:</legend>
                    <label for="track">Track
                        <input type="text" id="track" name="track" value="<?php echo isset($_POST['track']) ? htmlspecialchars(stripslashes($_POST['track'])) : ''; ?>" />
                    </label>
<!--                    <label for="artist">Artist
                        <input type="text" id="artist" name="artist" value="<?php echo isset($_POST['artist']) ? htmlspecialchars(stripslashes($_POST['artist'])) : ''; ?>" />
                    </label>
                    <label for="album">Album
                        <input type="text" id="album" name="album" value="<?php echo isset($_POST['album']) ? htmlspecialchars(stripslashes($_POST['album'])) : ''; ?>" />
                    </label>
-->
                    <hr />
                    <p><input type="submit" name="submit" value="Søk" /><input type="hidden" name="checkTime" value="<?php echo time(); ?>" /></p>
                </fieldset>
            </form>

<?php require_once("spotbox.php"); ?>

        </div>
    </body>
</html>