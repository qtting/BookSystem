<?php
session_start();
$_SESSION['id']=null;
$_SESSION['admin'] = null;
$id = $code = "";    
$id = $_POST["id"];         
$pwd = $_POST["pwd"];
  
    $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
    $pdo->exec('set names utf8');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare("select id from user where id = :id "); 
    $stmt->bindvalue(':id',$id, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if($result['id']==$id)
    {
     $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
        $pdo->exec('set names utf8');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $pdo->prepare("select pwd from user where id = :id "); 
        $stmt->bindvalue(':id',$id, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(password_verify($pwd,$result['pwd']))
        {
            $_SESSION['admin'] = true;
            $_SESSION['id'] = $id;
            echo "<script>alert('登录成功,'+$id);window.location.href='shome.html'</script>";
        }
        else {
            echo "<script>alert('请输入正确六位用户名或者正确的密码');window.location.href='login.html'</script>";
        }
    }
    else {
        echo "<script>alert('请输入正确的六位学号或者正确的密码');window.location.href='login.html'</script>";
    }

$pdo = null;
    ?>