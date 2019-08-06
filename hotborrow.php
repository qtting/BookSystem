<?php
session_start();
//数据库操作，找出最受欢迎的十本图书，其中前三的样式做区别s
if($_SESSION['admin']) 
{   
        $id = $_SESSION['id'];
        
        $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
        $pdo->exec('set names utf8');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $tagkind = $_POST['tagkind'];
        if($tagkind == 2){
                //根据读者打分情况
                $stmt = $pdo->prepare("select * from book ORDER BY totalpoints DESC"); 
                $stmt->bindvalue(':id',$id);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $j_result = json_encode($result);
                echo $j_result;
        }
        else if($tagkind == 1){
                //根据图书借阅次数
                $stmt = $pdo->prepare("select * from book ORDER BY times DESC"); 
                $stmt->bindvalue(':id',$id);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $j_result = json_encode($result);
                echo $j_result;
        }
        else if($tagkind == 3){
                //个人借阅榜
                $stmt = $pdo->prepare("select * from user ORDER BY times DESC"); 
                $stmt->bindvalue(':id',$id);
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

                $j_result = json_encode($result);
                echo $j_result;
        }
        $pdo = null;
        //使用AJAX将数据传回，然后显示借阅或者好评前十的图书
}
else echo "<script>alert('请先进行登录');window.location.href='slogin.html'</script>";
?>