<!-- File: /app/View/SpotifyTracks/index.ctp -->

<h1>Spotify Track Data</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Artist</th>
        <th>Album</th>
        <th>Length</th>
        <th>Popularity</th>
    </tr>

    <?php foreach ($spotify_tracks as $spotify_track): ?>
    <tr>
        <td><?php echo $spotify_track['SpotifyTrack']['uri']; ?></td>
        <td><?php echo $spotify_track['SpotifyTrack']['title']; ?></td>
        <td><?php echo $spotify_track['SpotifyTrack']['artist']; ?></td>
        <td><?php echo $spotify_track['SpotifyTrack']['album']; ?></td>
        <td><?php echo $spotify_track['SpotifyTrack']['length']; ?></td>
        <td><?php echo $spotify_track['SpotifyTrack']['popularity']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>
