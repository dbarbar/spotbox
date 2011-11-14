<h1>Search for Songs</h1>

<?php echo $this->Form->create(FALSE, array('type' => 'get')); ?>
<?php echo $this->Form->text('q'); ?>
<?php echo $this->Form->end('Search'); ?>

<?php if (count($results) > 0): ?>
<h2>Search Results</h2>
<table>
    <tr>
        <th>Id</th>
        <th>Title</th>
        <th>Artist</th>
        <th>Album</th>
        <th>Length</th>
        <th>Popularity</th>
    </tr>

    <?php foreach ($results as $track): ?>
    <tr>
        <td><?php echo $track->getURI(); ?></td>
        <td><?php echo $track->getTitle(); ?>
        <td><?php echo $track->getArtistAsString(); ?></td>
        <td><?php echo $track->getAlbum(); ?></td>
        <td><?php echo $track->getLengthInMinutesAsString(); ?></td>
        <td><?php echo $track->getPopularityAsPercent(); ?></td>
    </tr>
    <?php endforeach; ?>

</table>
<?php endif; ?>
