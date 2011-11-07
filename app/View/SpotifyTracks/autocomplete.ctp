<!-- File: /app/View/SpotifyTracks/autocomplete.ctp -->

<?php foreach ($spotify_tracks as $spotify_track): ?>
<?php echo $spotify_track['SpotifyTrack']['title']; ?> by <?php echo $spotify_track['SpotifyTrack']['artist']; ?>

<?php endforeach; ?>
