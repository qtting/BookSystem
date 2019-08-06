<?php
session_start();
//首先查询是否有其想要归还的书籍，如果有有，更新数据，否则返回
if($_SESSION['admin']) 
{
    
    if ($_SERVER["REQUEST_METHOD"] == "POST")
     {

        $id = $_SESSION['id'];
        $name = $_POST["name"];
        $author = $_POST["author"];
        $score = $_POST['score'];
        echo $score;
        
        $pdo = new PDO("mysql:host=localhost;dbname=BookSystem",'root','');
        $pdo->exec('set names utf8');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //检查是否借了这本书
        $bookreturn = 'No';
        $stmt = $pdo->prepare("select * from borrow where bookname = :name AND author = :author AND id = :id AND bookreturn = :bookreturn"); 
        $stmt->bindvalue(':name',$name);
        $stmt->bindvalue(':author',$author);
        $stmt->bindvalue(':id',$id);
        $stmt->bindvalue(':bookreturn',$bookreturn);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        print_r($result);


        if(!empty($result))
        {
            $stmt = $pdo->prepare("select * from book where bookname = :name AND author = :author"); 
            $stmt->bindvalue(':name',$name);
            $stmt->bindvalue(':author',$author);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $id = $_SESSION['id'];
            $bookreturn = "Yes";
            $exist = $result[0]['exist']+1;
            $actualreturn = date("Y-m-d");
            
            

            //读者评分
            $sql="UPDATE borrow set score = '{$score}' where bookname = '{$name}' AND author = '{$author}' AND id = '{$id}' AND bookreturn = 'No'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            //图书归还状态
            $sql="UPDATE borrow set bookreturn = '{$bookreturn}' where bookname = '{$name}' AND id = '{$id}' AND author = '{$author}'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            //更新book表中记录的书的现存数量
            $sql="UPDATE book set exist = '{$exist}' where bookname = '{$name}' AND author = '{$author}'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            

            //记录实际归还日期
            $sql="UPDATE borrow set actualreturn = '{$actualreturn}' where bookname = '{$name}' AND author = '{$author}' AND id = '{$id}'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            
            //每次归还都计算一遍图书所得分数，更新totalpoint
            $stmt = $pdo->prepare("select score from borrow where bookname = :name AND author = :author"); 
            $stmt->bindvalue(':name',$name);
            $stmt->bindvalue(':author',$author);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            print_r($result);
            //result内有该本书的读者评分
            //算得分的时候应该把-1去掉  
            $count = count($result);
            $flag = 0;
            $total = 0;
            $i = 0;
            for($i=0;$i<$count;$i++){
              if($result[$i]!=-1){
                 $total += $result[$i]['score'];
                 $flag++;
                 echo $total;
              }             
            }          

            $totalpoints = $total/$flag;
            echo $totalpoints;
            //更新book表内图书得分信息
            $sql="UPDATE book set totalpoints = '{$totalpoints}' where bookname = '{$name}' AND author = '{$author}'";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();

            $pdo = null;
          echo "<script>alert('归还成功');window.location.href='return.html'</script>"; 
        }
        else {
          echo "<script>alert('抱歉，您并没有借这本书，请重试');window.location.href='return.html'</script>";
        }
  }
}
?>