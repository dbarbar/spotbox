<p>Search for songs below and click "Request" to add them to the SpotiMonster playlist.</p>

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
    </tr>
    <?php endforeach; ?>

</table>
<?php endif; ?>

<?php if (count($tracks) > 0) : ?>

  <h2>Recently Added</h2>
      <?php foreach ($tracks as $spotify_track): ?>
      <p><?php echo $spotify_track['Track']['title']; ?> by <?php echo $spotify_track['Track']['artist']; ?><br />
      </p>
      <?php endforeach; ?>

<?php endif; ?>
