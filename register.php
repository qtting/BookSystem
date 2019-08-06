<?php
session_start();
$_SESSION['admin'] = null;
$_SESSION['id'] = null;
$id = $pwd = "";   
$id = $_POST["id"];         
$pwd = $_POST["pwd"];  
 
$pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
    $pdo->exec('set names utf8');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("select id from user where id = :id "); 
    $stmt->bindvalue(':id',$id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    if($result['id']!=$id)
    {
      $times = 0;
      $q = $pdo ->prepare('INSERT INTO user(`id`,`pwd`,`times`,`userkind`) VALUES (:id,:pwd,:times,:userkind)');
      $q -> bindValue(':id', $id); 
      $pwd = password_hash($pwd,PASSWORD_DEFAULT);
      $q -> bindValue(':pwd', $pwd);
      $q -> bindValue(':times', $times);
      $q -> bindValue(':userkind', 'S');
      $q -> execute();
      $_SESSION['admin'] = true;
      $_SESSION['id'] = $id;
      echo "<script>alert('注册成功，用户'+$id);window.location.href='shome.html'</script>";
    }
    else {
        echo "<script>alert('请换一个用户名');window.location.href='register.html'</script>";
    }
$pdo = null;
?>
