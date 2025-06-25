<?php
$showalert =false;
$showerror=false;
if($_SERVER["REQUEST_METHOD"]=="POST"){
  $err= "";
  include "partials/db_connect.php";
  $username = $_POST["username"];
  $password = $_POST["password"];
  $cpassword = $_POST["cpassword"];
  $exists=false;
  if(($password==$cpassword)&& $exists==false){
      $sql= "INSERT INTO `user` (`username`, `password`, `dt`) VALUES ('$username', '$password', current_timestamp())";
      $result= mysqli_query($conn,$sql);
      if($result){
        $showalert= true;
      }
    }
    else{
      $showerror="password do not match";
    }
}

?>


<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sing Up</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
  </head>
  <body>
    
    <?php require 'partials/_nav.php'?>
    <?php
     if($showalert){
         echo '<div class="alert" alert-success alert-dismissible fade show" role="alert">
      <strong>success</strong> Your account is now created now you can login
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
     }
     if($showerror){
         echo '<div class="alert" alert-danger alert-dismissible fade show" role="alert">
      <strong>error</strong> password do not match
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>';
     }
    ?>
    <div class="container my-4">
        <h1 class="text-center"> Sing up to our website </h1>
    <form action="sing_up.php" method="post">
    <div class="mb-3">
    <label for="username">username</label>
    <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp">
  </div>
  <div class="mb-3">
    <label for="password" class="form-label">Password</label>
    <input type="password" class="form-control" id="password" name="password">
  </div>
  <div class="mb-3">
    <label for="cpassword" class="form-label">Conform Password</label>
    <input type="password" class="form-control" id="cpassword" name="cpassword">
   <div id="emailHelp" class="form-text">Make sure you type same password.</div>
  </div>
  
  <button type="submit" class="btn btn-primary">Submit</button>
</form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
  </body>
</html> 