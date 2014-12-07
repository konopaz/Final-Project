<?php
require_once 'inc/initialize.php';

$loginFeedback = '';
$signupFeedback = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {

  if($_POST['action'] == 'login') {

    if(strlen($_POST['username']) == 0 || strlen($_POST['password']) == 0) {

      $loginFeedback = "Please enter your ursername and password.\n";
    }

    if(strlen($loginFeedback) == 0) {

      $sql = 'SELECT id, password FROM cs494_users WHERE username = :username';
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':username', $_POST['username']);
      $stmt->execute();

      if($row = $stmt->fetch()) {

        if($row['password'] == $_POST['password']) {

          $_SESSION['userId'] = $row['id'];

          if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

            $obj = new stdClass;
            $obj->status = 'redirect';
            $obj->url = 'index.html';

            header('Content-Type: application/json');
            echo json_encode($obj);
          }
          else {

            header('Location: index.html');
          }

          exit;
        }
        else {

          $loginFeedback = "Sorry, password didn't match.\n";
        }
      }
      else {

        $loginFeedback = "Sorry, couldn't find a user with that username.\n";
      }
    }

    if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

      $obj = new stdClass;
      $obj->status = 'error';
      $obj->feedback = $loginFeedback;

      header('Content-Type: application/json');
      echo json_encode($obj);
      exit;
    }
  }

  if($_POST['action'] == 'signup') {

    if(strlen($_POST['newUsername']) == 0) {
      $signupFeedback .= "Please enter your new username.\n";
    }

    if(strlen($_POST['newPassword']) == 0 || strlen($_POST['confirmPassword']) == 0) {
      $signupFeedback .= "Please enter your new password.\n";
    }
    elseif($_POST['newPassword'] != $_POST['confirmPassword']) {
      $signupFeedback .= "Please confirm your new password.\n";
    }

    if(strlen($signupFeedback) == 0) {

      $sql = "INSERT INTO cs494_users (username, password) VALUES (:username, :password)";
      $stmt = $pdo->prepare($sql);
      $stmt->bindParam(':username', $_POST['newUsername']);
      $stmt->bindParam(':password', $_POST['newPassword']);

      if($stmt->execute()) {

        $_SESSION['userId'] = $pdo->lastInsertId();

        if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

          $obj = new stdClass;
          $obj->status = 'redirect';
          $obj->url = 'index.html';

          header('Content-Type: application/json');
          echo json_encode($obj);
        }
        else {

          header('Location: index.html');
        }

        exit;
      }
      else {

        $signupFeedback = "Please enter a different username.\n";
      }
    }

    if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {

      $obj = new stdClass;
      $obj->status = 'error';
      $obj->feedback = $signupFeedback;

      header('Content-Type: application/json');
      echo json_encode($obj);
      exit;
    }
  }

}

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>My Movies Database</title>
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap-theme.min.css">
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h2>Login</h2>
          <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
            <?php if(strlen($loginFeedback)): ?>
              <p><?=nl2br($loginFeedback)?></p>
            <?php endif; ?>
            <input type="hidden" name="action" value="login"/>
            <div class="form-group">
              <label for="username">Username</label>
              <input type="text" name="username" class="form-control" id="username" value="<?=h($_POST['username'])?>"/>
              <label for="password">Password</label>
              <input type="password" name="password" class="form-control" id="password" autocomplete="off"/>
            </div>
            <input type="submit" value="Login" class="btn btn-primary"/>

          </form>
        </div>
        <div class="col-md-4">
          <h2>Signup</h2>
          <form method="post" action="<?=$_SERVER['PHP_SELF']?>">
            <?php if(strlen($signupFeedback)): ?>
              <p><?=nl2br($signupFeedback)?></p>
            <?php endif; ?>
            <input type="hidden" name="action" value="signup"/>

            <div class="form-group">
              <label for="newUsername">New Username</label>
              <input type="text" name="newUsername" class="form-control" id="newUsername" value="<?=h($_POST['newUsername'])?>"/>
              <label for="newPassword">New Password</label>
              <input type="password" name="newPassword" class="form-control" id="newPassword" value="" autocomplete="off"/>

              <label for="confirmPassword">Confirm Password</label>
              <input type="password" name="confirmPassword" class="form-control" id="confirmPassword" value="" autocomplete="off"/>
            </div>
            <input type="submit" value="Signup" class="btn btn-primary"/>

        </div>
      </div>
    </div>
    <script type="text/javascript" src="http://code.jquery.com/jquery.min.js"></script>
    <script type="text/javascript" src="bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript">
    //<![CDATA[
      $(document).ready(function() {

        $('form').submit(function(e) {

          var $this = $(this);

          e.preventDefault();

          $.post($this.attr('action'), $this.serialize(), function(resp, stat) {

            if(resp.status == 'error') {

              alert(resp.feedback);
            }
            else if(resp.status == 'redirect') {

              window.location = resp.url;
            }
          });

          return false;
        });
      });
    //]]>
    </script>
  </body>
</html>
