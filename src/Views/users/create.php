<form action="./users/<?php echo htmlspecialchars($user['id']) ?>" method="post">
<!--     <input type="hidden" name="_METHOD" value="PUT"> -->
   <?= $formHtml ?>
   <button type="submit">Save Changes</button>
</form>
