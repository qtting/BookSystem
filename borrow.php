<?php
session_start();
//首先查询是否有其想要借阅的书籍，其次看书籍是否有剩，如果有，插入数据并且书籍现有数量减1
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
        //检查书库中是否有这本书
        $stmt = $pdo->prepare("select * from book where bookname = :name AND author = :author"); 
        $stmt->bindvalue(':name',$name);
        $stmt->bindvalue(':author',$author);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $exist = $result[0]['exist']-1;
        $times = $result[0]['times']+1;


        if(!empty($result))
        {
          //检查图书是否到达上限
          $stmt = $pdo->prepare("select * from borrow where id = :id"); 
          $stmt->bindvalue(':id',$id);
          $stmt->execute();
          $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          if(count($result)<30)
          {
                $stmt = $pdo->prepare("select * from borrow where bookname = :name AND author = :author AND id = :id AND bookreturn=:bookreturn"); 
                $stmt->bindvalue(':name',$name);
                $stmt->bindvalue(':author',$author);
                $stmt->bindvalue(':id',$id);
                $stmt->bindvalue(':bookreturn',"No");
                $stmt->execute();
                $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if(empty($result))
                {
                  $borrowdate = date("Y-m-d");
                  $returndate = date("Y-m-d", strtotime("+1 months", strtotime(date("Y-m-d"))));
                  $actualreturn = date("Y-m-d", strtotime("-1 months", strtotime(date("Y-m-d"))));

                  //用户借阅次数加一
                  $stmt = $pdo->prepare("select * from user where id = :id"); 
                  $stmt->bindvalue(':id',$id);
                  $stmt->execute();
                  $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
          
                  $usertimes = $result[0]['times']+1;

                  $q = $pdo ->prepare('INSERT INTO borrow(`bookname`,`author`,`borrowdate`,`returndate`,`bookreturn`,`id`,`score`,`actualreturn`) VALUES (:bookname,:author,:borrowdate,:returndate,:bookreturn,:id,:score,:actualreturn)');
                  $q -> bindValue(':bookname', $name); 
                  $q -> bindValue(':author', $author); 
                  $q -> bindValue(':borrowdate', $borrowdate); 
                  $q -> bindValue(':returndate', $returndate); 
                  $q -> bindValue(':bookreturn', 'No'); 
                  $q -> bindValue(':id', $_SESSION['id']);
                  $q -> bindValue(':score', -1);
                  $q -> bindValue(':actualreturn', $actualreturn);
                  $q -> execute();

                  
                  $sql="UPDATE book set exist = '{$exist}' where bookname = '{$name}' AND author = '{$author}'";
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  $sql="UPDATE book set times = '{$times}' where bookname = '{$name}' AND author = '{$author}'";
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  $sql="UPDATE user set times = '{$usertimes}' where id = '{$id}'";
                  $stmt = $pdo->prepare($sql);
                  $stmt->execute();

                  $pdo = null;
                        echo "<script>alert('借阅成功');window.location.href='borrow.html'</script>";
                  }
                  else{
                    echo "<script>alert('您已经借过这本书，试试其他书籍吧~');window.location.href='borrow.html'</script>";
                  }  
          }
          else {
            echo "<script>alert('您的借书量已到达上限');window.location.href='borrow.html'</script>";
          }
        }
        else {
          echo "<script>alert('书库中不存在这本书，抱歉，请重试');window.location.href='borrow.html'</script>";
        }
  }
}
else echo "<script>alert('请先进行登录');window.location.href='slogin.html'</script>";
?>