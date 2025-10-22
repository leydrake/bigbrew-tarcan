
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel ="stylesheet" href ="./table.css">
    <script>
        document.getElementById("update").style.display = "block";
    </script>
</head>
<body>
    
    <div class="update-container" id="update">
    <form action="table.php" method="POST">
        ID: 
        <input type="text" name="update_txt" required> <br>
        YEAR: 
        <select name="yearss" id="year" required> <br>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
       </select> <br>
       <input type="submit" value="update" name="update2">
       </form>
    </div>
    
   

    
    <div class="delete-container" id="delete">
    <form action="table.php" method="POST">
        ID: 
        <input type="text" name="delete3" required>
        <input type="submit" value="delete" name="delete2">
        </form>

    </div>
    
    
</body>
</html>

<?php 
    include ("database.php");
    $result = mysqli_query($conn, "SELECT * FROM student");

    echo "<table>
        <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>YEAR</th>
            <th>WEB</th>
            <th>OOP</th>
            <th>DBMS</th>
        </tr>
        ";
        while ($row = mysqli_fetch_array($result)){
            $temp1 = $row['id'];
            $temp2 = $row['studentName'];
            $temp3 = $row['Years'];
            $temp4 = $row['WEB'];
            $temp5 = $row['OOP'];
            $temp6 = $row['DBMS'];

            $temp41 = "";
            $temp51 = "";
            $temp61 = "";

            $tempcolor1 = "";
            $tempcolor2= "";
            $tempcolor3 = "";


            if($temp4 < 74){
                $temp41 = "failed";
                $tempcolor1 = "red";
            }else{
                $tempcolor1 = "green";
                $temp41 = "passed";
            }

            if($temp5 < 74){
                $tempcolor2 = "red";
                $temp51 = "failed";
            }else{
                $tempcolor2= "green";
                $temp51 = "passed";
            }
            
            if($temp6 < 74){
                $tempcolor1 = "";
                $temp61 = "failed";
            }else{
                $temp61 = "passed";
            }


            echo "<tr>
                    <td>
                    $temp1
                    </td>
                    <td>
                    $temp2
                    </td>
                    <td>
                    $temp3
                    </td>
                    <td>
                    $temp41
                    </td>
                    <td>
                    $temp51
                    </td>
                    <td>
                    $temp61
                    </td>
                  </tr>";
                    
        }
  echo "</table>";
        echo "<form action='table.php' method ='POST'>";
            echo "<input type='submit' value='add'name='add'>";
            echo "<input type='submit' value='update'name='update'>";
            echo "<input type='submit' value='delete'name='delete'>";
         echo "</form>";
    if (isset($_POST['add'])){
        echo "<script type = 'text/javascript'>
        window.location.href = 'index.php';
         
     </script>";

    }
    if (isset($_POST['update'])){
        echo "<script>
        document.getElementById('update').style.display = 'block';
        document.getElementById('delete').style.display = 'none';
        </script>";

    }
    if(isset($_POST['update2'])){
        $sql1 = "UPDATE student SET Years = '$_POST[yearss]' WHERE id = '$_POST[update_txt]'";
    
         mysqli_query($conn, $sql1);
     }  
    
     if(isset($_POST['delete2'])){
        $sql1 = "Delete From student WHERE id = $_POST[delete3]";
    
         mysqli_query($conn, $sql1);
     }
    
    if (isset($_POST['delete'])){
        echo "<script>
        document.getElementById('delete').style.display = 'block';
        document.getElementById('update').style.display = 'none';
         </script>";


    }

?>

