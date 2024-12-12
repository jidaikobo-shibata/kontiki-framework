<table class="table table-striped table-bordered table-hover">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Username</th>
            <th scope="col">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($users as $user) : ?>
        <tr>
            <td><?php echo htmlspecialchars($user['id']) ?></td>
            <td><?php echo htmlspecialchars($user['username']) ?></td>
            <td>
                <a href="./users/<?php echo $user['id'] ?>/edit" class="btn btn-primary btn-sm">Edit</a>
                <form action="./users/<?php echo $user['id'] ?>" method="post" class="d-inline">
                    <input type="hidden" name="_METHOD" value="DELETE">
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
