<?php
session_start();
//首先查询书库中是否已有这种书籍，如果有则删除数据，如果没有则提示并跳转
if($_SESSION['admin']) 
{
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $id = $_SESSION['id'];
        $name = $_POST["name"];
        $author = $_POST["author"];
        
        $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
        $pdo->exec('set names utf8');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        //检查用户是否是管理员
        $stmt = $pdo->prepare("select * from user where id = :id AND userkind = :userkind"); 
        $stmt->bindvalue(':id',$_SESSION['id']);
        $stmt->bindvalue(':userkind','A');
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(empty($result))
        {
          echo "<script>alert('您并不是管理员，请进行登录');window.location.href='login.html'</script>"; 
        }
        else{
          //检查书库中是否有这本书
        $stmt = $pdo->prepare("select bookname from book where bookname = :name AND author = :author"); 
        $stmt->bindvalue(':name',$name);
        $stmt->bindvalue(':author',$author);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($result))//有
        {
            $q = $pdo ->prepare('DELETE from book where bookname = :bookname AND author = :author');
            $q -> bindValue(':bookname', $name);
            $q -> bindValue(':author', $author);
            $q -> execute();

            //删除kind表中图书的相应数据
            $q = $pdo ->prepare('DELETE from kind where bookname = :bookname AND author = :author');
            $q -> bindValue(':bookname', $name);
            $q -> bindValue(':author', $author);
            $q -> execute();

            echo "<script>alert('删除成功');window.location.href='delete.html'</script>";  
            $pdo = null; 
        }
        else {
          echo "<script>alert('书库无这本书');window.location.href='delete.html'</script>";
        }
        }
        
  }
}
?>