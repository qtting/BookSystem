<?php
session_start();

$pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
$pdo->exec('set names utf8');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare("select * from user where id = :id AND userkind = :userkind"); 
$stmt->bindvalue(':id',$_SESSION['id']);
$stmt->bindvalue(':userkind','A');
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if(!empty($result))
{   
    echo "<script>window.location.href='ahome.html'</script>";
}
else {
    echo "<script>alert('您并不是管理员');window.location.href='shome.html'</script>";
}

$pdo = null;
    ?>