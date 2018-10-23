<?php
include('database.php');

$title = 'Todo Detail';
include('_top.php');
?>
<p><a href="index.php">Terug naar overzicht</a></p>
<?php
if (isset($_GET['id'])) {
  $sql = "SELECT * FROM `todos` WHERE `id` = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':id', $_GET['id']);
  $stmt->execute();
  $todo = $stmt->fetch(PDO::FETCH_ASSOC);
}
if (empty($todo)) {
  echo '<p>Deze todo kon niet gevonden worden</p>';
} else {

  $errors = array();
  if (!empty($_POST)) {
    if (empty($_POST['text'])) {
      $errors['text'] = 'Gelieve een tekst in te vullen';
    }
  }

  if (!empty($_POST) && empty($errors)) {
    $sql = "INSERT INTO `todo_comments` (`created`, `modified`, `todo_id`, `text`) VALUES (:created, :modified, :todo_id, :text)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':created', date('Y-m-d H:i:s'));
    $stmt->bindValue(':modified', date('Y-m-d H:i:s'));
    $stmt->bindValue(':todo_id', $todo['id']);
    $stmt->bindValue(':text', $_POST['text']);
    $stmt->execute();
  }

  if (!empty($_GET['action'])) {
    if ($_GET['action'] == 'delete_comment' && isset($_GET['comment_id'])) {
      $sql = "DELETE FROM `todo_comments` WHERE `id` = :id";
      $stmt = $pdo->prepare($sql);
      $stmt->bindValue(':id', $_GET['comment_id']);
      $stmt->execute();
    }
  }

  $sql = "SELECT * FROM `todo_comments` WHERE `todo_id` = :todo_id";
  $stmt = $pdo->prepare($sql);
  $stmt->bindValue(':todo_id', $_GET['id']);
  $stmt->execute();
  $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo '<p>Todo: ' . $todo['text'] . ' (' . $todo['created'] . ')</p>';
  echo '<section>';
  echo '<header><h2>Comments op deze todo:</h2></header>';

  if (empty($comments)) {
    echo '<p>Er werden nog geen comments geplaatst</p>';
  } else {
    echo '<ol>';
    foreach ($comments as $comment) {
      echo '<li>';
      echo $comment['created'];
      echo ': ';
      echo $comment['text'];
      echo ' <a href="todo-detail.php?id=' . $todo['id'] . '&amp;action=delete_comment&amp;comment_id=' . $comment['id'] . '" class="confirmation">delete</a>';
      echo '</li>';
    }
    echo '</ol>';
  }
  echo '</section>';

  if (!empty($_POST) && !empty($errors)) {
    echo '<div class="error">Gelieve de verplichte velden in te vullen</div>';
  }
  if (empty($_POST) || !empty($errors)) {
  ?>
  <form method="post" action="todo-detail.php?id=<?php echo $todo['id'];?>">
    <div>
      <label for="inputText">comment:</label>
      <textarea id="inputText" name="text"><?php
      if (!empty($_POST['text'])) {
        echo $_POST['text'];
      }
      ?></textarea>
      <?php
      if (!empty($errors['text'])) {
        echo '<span class="error">' . $errors['text'] . '</span>';
      }
      ?>
    </div>
    <div>
      <button type="submit">Add Comment</button>
    </div>
  </form>
  <?php
  } else {
  ?>
  <p>De comment werd toegevoegd!</p>
  <?php
  }
}
?>
<script type="text/javascript">
{
  const init = () => {
    const confirmationLinks = Array.from(document.getElementsByClassName(`confirmation`));
    confirmationLinks.forEach($confirmationLink => {
      $confirmationLink.addEventListener(`click`, e => {
        if (!confirm('Are you sure?')) e.preventDefault();
      });
    });
  };
  init();
}
</script>
<?php include('_bottom.php');?>
