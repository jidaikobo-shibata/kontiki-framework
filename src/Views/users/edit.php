<h1><?php echo htmlspecialchars($pageTitle) ?></h1>
<form action="./users/<?php echo htmlspecialchars($user['id']) ?>" method="post">
    <input type="hidden" name="_METHOD" value="PUT">
    <div>
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user['username']) ?>" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password">
    </div>
    <button type="submit">Save Changes</button>
</form>
