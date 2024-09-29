<?php

include '../components/connect.php';

if(isset($_COOKIE['admin_id'])){
   $admin_id = $_COOKIE['admin_id'];
}else{
   $admin_id = '';
   header('location:login.php');
}

if(isset($_POST['delete'])){

   $delete_id = $_POST['delete_id'];
   $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

   $verify_delete = $conn->prepare("SELECT * FROM `admins` WHERE id = ?");
   $verify_delete->execute([$delete_id]);

   if($verify_delete->rowCount() > 0){
      $delete_admin = $conn->prepare("DELETE FROM `admins` WHERE id = ?");
      $delete_admin->execute([$delete_id]);
      $success_msg[] = 'Admin deleted!';
   }else{
      $warning_msg[] = 'Admin deleted already!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admins</title>

   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css">

   <link rel="stylesheet" href="../css/admin_style.css">

</head>
<body>
   
<?php include '../components/admin_header.php'; ?>

<section class="grid">

   <h1 class="heading">Admins</h1>

   <div class="box-container">

   <div class="box" style="text-align: center;">
      <p>Create a New Admin</p>
      <a href="register.php" class="btn">register now</a>
   </div>

   <?php
      $select_admins = $conn->prepare("SELECT * FROM `admins`");
      $select_admins->execute();
      if($select_admins->rowCount() > 0){
         while($fetch_admins = $select_admins->fetch(PDO::FETCH_ASSOC)){
   ?>
   <div class="box" <?php if( $fetch_admins['name'] == 'admin'){ echo 'style="display:none;"'; } ?>>
      <p>Name : <span><?= $fetch_admins['name']; ?></span></p>
      <form action="" method="POST">
         <input type="hidden" name="delete_id" value="<?= $fetch_admins['id']; ?>">
         <input type="submit" value="delete admins" onclick="return confirm('delete this admin?');" name="delete" class="btn">
      </form>
   </div>
   <?php
      }
   }else{
   }
   ?>

   </div>

</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

<script src="../js/admin_script.js"></script>

<?php include '../components/message.php'; ?>

</body>
</html>