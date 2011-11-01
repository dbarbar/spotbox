<?php
header("Content-Type: text/plain; charset=UTF-8");
print file_get_contents("/var/www/spotbox-playlists/tracklists.txt");
