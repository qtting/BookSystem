<?php
session_start();
//首先查询书库中是否已有这种书籍，如果有则直接跳转增加数量，如果没有则插入数据
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
        if(empty($result))//如果书库中没有这本书
        {
          $exist = $number;
          $times = 0;//借阅次数零
          $totalpoints = 0;//得分为零

          $q = $pdo ->prepare('INSERT INTO book(`bookname`,`author`,`booknumber`,`exist`,`handler`,`times`,`totalpoints`) VALUES (:bookname,:author,:booknumber,:exist,:id,:times,:totalpoints)');
          $q -> bindValue(':bookname', $name); 
          $q -> bindValue(':author', $author); 
          $q -> bindValue(':booknumber', $number); 
          $q -> bindValue(':exist', $exist); 
          $q -> bindValue(':id', $_SESSION['id']);
          $q -> bindValue(':times', $times);
          $q -> bindValue(':totalpoints', $totalpoints);
          $q -> execute();
          
          //处理图书种类的数据
          $kind = $_POST['kind'];
          for($i=0;$i< count($kind);$i++)
          {
            //在kind表中插入图书种类
              $q = $pdo ->prepare('INSERT INTO kind(`bookname`,`author`,`handler`,`kind`) VALUES (:bookname,:author,:id,:kind)');
              $q -> bindValue(':bookname', $name); 
              $q -> bindValue(':author', $author); 
              $q -> bindValue(':id', $_SESSION['id']);
              $q -> bindValue(':kind', $kind[$i]);
              $q -> execute();
          }
          $pdo = null;
          echo "<script>alert('增加成功');window.location.href='increase.html'</script>"; 
        }
        else {
          echo "<script>alert('书库已存在这本书');window.location.href='increase.html'</script>";
        }
        }
        
  }
}
?>