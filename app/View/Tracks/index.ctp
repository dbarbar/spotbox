
<h1>Currently on the playlist</h1>
<table>
    <tr>
        <th>Title</th>
        <th>Artist</th>
        <th>Album</th>
    </tr>

    <?php foreach ($tracks as $spotify_track): ?>
    <tr>
        <td><?php echo $spotify_track['Track']['title']; ?></td>
        <td><?php echo $spotify_track['Track']['artist']; ?></td>
        <td><?php echo $spotify_track['Track']['album']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>
