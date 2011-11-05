<!-- File: /app/View/Requests/index.ctp -->

<h1>Track Requests</h1>
<table>
    <tr>
        <th>Id</th>
        <th>Created</th>
        <th>Modified</th>
        <th>Request Count</th>
    </tr>

    <?php foreach ($requests as $request): ?>
    <tr>
        <td><?php echo $request['Request']['uri']; ?></td>
        <td><?php echo $request['Request']['created']; ?></td>
        <td><?php echo $request['Request']['modified']; ?></td>
        <td><?php echo $request['Request']['request_count']; ?></td>
    </tr>
    <?php endforeach; ?>

</table>
