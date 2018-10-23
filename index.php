<?php
/**
 * cfr. todo-detail.php
 */
include('database.php');

$title = 'Overview';
include('_top.php');
?>
<ol>
  <?php
  $sql = "SELECT * FROM `todos`";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $todos = $stmt->fetchAll(PDO::FETCH_ASSOC);

  foreach ($todos as $todo) {
   echo '<li>';
   echo $todo['text'];
   echo ' <a href="todo-detail.php?id=' . $todo['id'] . '">detail</a>';
   echo '</li>';
  }
  ?>
</ol>
<?php include('_bottom.php');?>
