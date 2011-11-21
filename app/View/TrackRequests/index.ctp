
<h1>Track Requests</h1>

<p>These are pending additions to the SpotiMonster Requests playlist.  They will be added at the next scheduled batch update.</p>

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
