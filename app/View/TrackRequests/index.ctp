
<h1>Track Requests</h1>

<p><?php echo $this->Html->link('Click here for a list of requests to paste into Spotify.', array('controller' => 'TrackRequests', 'action' => 'textlist')); ?></p>

<p><?php echo $this->Html->link('Click here to clear the play queue.', array('controller' => 'TrackRequests', 'action' => 'clear_all')); ?>  Do this after you paste the list above into Spotify.</p>

<table>
    <tr>
        <th>Id</th>
        <th>Created</th>
        <th>Modified</th>
        <th>Request Count</th>
    </tr>

    <?php foreach ($requests as $request): ?>
    <tr>
        <td><?php echo $request['TrackRequest']['id']; ?></td>
        <td><?php echo $request['TrackRequest']['created']; ?></td>
        <td><?php echo $request['TrackRequest']['modified']; ?></td>
        <td><?php echo $request['TrackRequest']['request_count']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>
