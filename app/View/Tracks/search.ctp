<p>Search for songs below and click "Request" to add them to the Fierce jukebox queue.</p>

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
    <tr>
        <td><?php echo $this->Html->link('Request', array('controller' => 'TrackRequests', 'action' => 'add', $track->getURI()))?>
        <td><?php echo $track->getTitle(); ?>
        <td><?php echo $this->Html->link($track->getArtistAsString(), '/?q=' . urlencode($track->getArtistAsString())); ?></td>
        <td><?php echo $this->Html->link($track->getAlbum(), '/?q=' . urlencode($track->getAlbum())); ?></td>
        <td><?php echo $track->getLengthInMinutesAsString(); ?></td>
        <td><?php echo $track->getPopularityAsPercent(); ?></td>
    </tr>
    <?php endforeach; ?>

</table>
<?php endif; ?>
