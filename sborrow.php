<?php
session_start();
//根据用户ID查询到其所借图书的具体信息
if($_SESSION['admin']) 
{   
        $id = $_SESSION['id'];
        //打开数据库
        $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
        $pdo->exec('set names utf8');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                $stmt = $pdo->prepare("select * from borrow where id = :id ORDER BY borrowdate DESC"); 
                $stmt->bindvalue(':id',$id);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);       
                $j_result = json_encode($result);
                echo $j_result;
        
    
        $pdo = null;
        //使用AJAX将数据传回，然后显示用户所借图书信息
}
else echo "<script>alert('请先进行登录');window.location.href='login.html'</script>";
?>