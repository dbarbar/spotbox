<h2>Search</h2>
<?php echo $this->Form->create(FALSE, array('type' => 'get')); ?>
<?php echo $this->Form->text('q'); ?>
<?php echo $this->Form->end('Search'); ?>

<div style="clear: both; height: 1em;"></div>

<?php if ($q && count($results) == 0): ?>
  <h2>No Results found</h2>
<?php endif; ?>


<?php if (count($results) > 0): ?>

<?php 
if (isset($album_name)) {
  print "<h2>$album_name";
}
else {
  print "<h2>Search Results</h2>";
}
?>

<table>
    <tr>
        <th>Request</th>
        <th>Title</th>
        <th>Artist</th>
        <th>Album</th>
    </tr>

    <?php foreach ($results as $track): ?>
    <tr>
      <?php
        if ($track['requested']) {
          $request_cell = 'Requested';
        }
        else {
          $request_cell = $this->Html->link('Request', array('controller' => 'TrackRequests', 'action' => 'add', $track['uri']));
        }
      ?>
        <td><?php echo $request_cell; ?>
        <td><?php echo $track['title']; ?>
        <td><?php echo $this->Html->link($track['artist'], '/?q=' . urlencode($track['artist'])); ?></td>
        <td><?php echo $this->Html->link($track['album'], '/?q=' . urlencode($track['album'])); ?></td>
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
