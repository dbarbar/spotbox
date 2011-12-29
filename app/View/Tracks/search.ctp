<p>Search for songs below and click "Request" to add them to the SpotiMonster playlist.</p>

<p><strong>New:</strong> December 19, 2011 - The search results now only show songs that are playable in the US.  Previously all songs were shown, which resulted in requests that never got played.  Sorry about that.  -- David

<h2>Search</h2>
<?php echo $this->Form->create(FALSE, array('type' => 'get')); ?>
<?php echo $this->Form->text('q'); ?>
<?php echo $this->Form->end('Search'); ?>

<?php if ($q && count($results) == 0): ?>
  <h2>No Results found</h2>
<?php endif; ?>

<?php if (count($results) > 0): ?>
<h2>Search Results</h2>
<table>
    <tr>
        <th>Request</th>
        <th>Title</th>
        <th>Artist</th>
        <th>Album</th>
        <th>Length</th>
        <th>Popularity</th>
    </tr>

    <?php foreach ($results as $track): ?>
      <?php
        if (!$track->getAlbum()->isAvailable('US')) {
          continue;
        }
      ?>
    <tr>
        <td>
        <?php
          echo $this->Html->link('Request', array('controller' => 'TrackRequests', 'action' => 'add', $track->getURI()));
        ?>
        <td><?php echo $track->getTitle(); ?>
        <td><?php echo $this->Html->link($track->getArtistAsString(), '/?q=' . urlencode($track->getArtistAsString())); ?></td>
        <td><?php echo $this->Html->link($track->getAlbum(), '/?q=' . urlencode($track->getAlbum())); ?></td>
        <td><?php echo $track->getLengthInMinutesAsString(); ?></td>
        <td><?php echo $track->getPopularityAsPercent(); ?></td>
    </tr>
    <?php endforeach; ?>

</table>
<?php endif; ?>

<?php if (count($tracks) > 0) : ?>

  <h2>Most Recently Requested</h2>
  <p>(10 to 20 minute delay)</p>
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


<?php endif; ?>
