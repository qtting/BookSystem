<?php
session_start();
$_SESSION['admin'] = null;
$_SESSION['id'] = null;
$id = $pwd = "";   
$id = $_POST["id"];         
$pwd = $_POST["pwd"];  
$verification = $_POST["pwd3"];

    $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
    $pdo->exec('set names utf8');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("select pwd from auser where number = :id "); 
    $stmt->bindvalue(':id',1, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    print_r($result);
    if(password_verify($verification,$result['pwd']))
    {
            //检查用户是否存在
            $stmt = $pdo->prepare("select * from user where id = :id"); 
            $stmt->bindvalue(':id',$_SESSION['id']);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if(empty($result))
            {
                $q = $pdo ->prepare('INSERT INTO user(`id`,`pwd`,`times`,`userkind`) VALUES (:id,:pwd,:times,:userkind)');
                $q -> bindValue(':id', $id); 
                $pwd = password_hash($pwd,PASSWORD_DEFAULT);
                $q -> bindValue(':pwd', $pwd);
                $q -> bindValue(':times', 0);
                $q -> bindValue(':userkind', 'A');
                $q -> execute();
                $_SESSION['admin'] = true;
                $_SESSION['id'] = $id;
                echo "<script>alert('注册成功，管理员'+$id);window.location.href='ahome.html'</script>";
                
            }
            else{
                echo "<script>alert('请换一个用户名');window.location.href='aregister.html'</script>"; 
            } 
    }
    else echo "<script>alert('验证密码错误');window.location.href='aregister.html'</script>";
    
$pdo = null;
?>
