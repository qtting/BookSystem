<?php
session_start();
//首先查询书库中是否已有这种书籍，如果有则进行修改，没有的话就提醒
if($_SESSION['admin']) 
{
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
        $id = $_SESSION['id'];
        $name = $_POST["name"];
        $author = $_POST["author"];
        $number = $_POST['booknumber'];
        
        $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
        $pdo->exec('set names utf8');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //检查书库中是否有这本书
        $stmt = $pdo->prepare("select bookname from book where bookname = :name AND author = :author"); 
        $stmt->bindvalue(':name',$name);
        $stmt->bindvalue(':author',$author);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(!empty($result))
        {
            $exist = $number;
            //由于涉及到现有图书，所以在修改图书数量时应该注意这一点，待完成 
            $sql="UPDATE book set booknumber = '{$number}' where bookname = '{$name}' AND author = '{$author}'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();        
            echo "<script>alert('修改成功');window.location.href='modification.html'</script>"; 
        }
        else {
          echo "<script>alert('书库不存在这本书，请到图书增加中进行增加');window.location.href='modification.html'</script>";
        }
        $pdo = null;
  }
}
?>