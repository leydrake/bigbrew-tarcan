<?php
       // test if admin
 $admin_email = 'dranskyxd@gmail.com';
 if(strval($_SESSION['emailAddress']) != $admin_email){
     
     echo "<script> alert('You don't have permission to access this.');
          window.location.href = './index.php'; </script>
     ";
     exit();
 }

     
 
?>