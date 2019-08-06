<?php
session_start();
if($_SESSION['admin']) 
{
    if ($_SERVER["REQUEST_METHOD"] == "POST")
    {
      $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
      $pdo->exec('set names utf8');
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      $kind = $_POST['kind'];
      if($kind == 'bookname')
      {
        $bookname = $_POST['content'];
        $stmt = $pdo->prepare("select * from book where bookname LIKE '{$bookname}%'"); 
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result)) ;
          else{
            $j_result = json_encode($result);
            echo $j_result;
            $pdo = null;
          }
      }
      else if($kind == 'author')
      {
        $author = $_POST['content'];
        $stmt = $pdo->prepare("select * from book where author LIKE '{$author}%'"); 
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result)) ;
          else{
            $j_result = json_encode($result);
            echo $j_result;
            $pdo = null;
          }
      }
      else if($kind == 'kind')
      {
        $kind = $_POST['content'];
        $stmt = $pdo->prepare("select * from kind where kind LIKE '%{$kind}%'"); 
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if(empty($result)) ;
          else{
            $j_result = json_encode($result);
            echo $j_result;
            $pdo = null;
          }
      }
    }
}
else {echo "<script>alert('请先进行登录');window.location.href='login.html'</script>";}
?>