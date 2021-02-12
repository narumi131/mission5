<!DOCTYPE html>
<html lang="ja">
<head>
    <mata charset="UTF-8">
    <title>mission5_1</title>
</head>
<body>
    <?php
    
    $table=mission5_1;
    
    //データベース接続
    $dsn = 'データベース名';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, 
	array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	
	$sql = "CREATE TABLE IF NOT EXISTS ".$table
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
	. "date TEXT,"
	. "pass char(32)"
	.");";
	$stmt = $pdo->query($sql);
    
    //投稿機能開始
    if(!empty($_POST['name'])
    &&(!empty($_POST['comment']))
    &&(empty($_POST['editnum']))
    &&(!empty($_POST['pass']))){
        //名前・コメント・日付・パスワードを入力
        $sql = $pdo -> prepare("INSERT INTO ".$table. 
        " (name, comment, date, pass) 
        VALUES (:name, :comment, :date, :pass)");
	    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
	    $sql -> bindParam(':date', $date, PDO::PARAM_STR);
	    $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);

        $name=$_POST['name'];
        $comment=$_POST['comment'];
        $date=date('Y/m/d H:i:s');
        $pass=$_POST['pass'];	   
        
	    $sql -> execute();
	//編集選択開始
    }elseif(!empty($_POST["ednum"])
    &&empty($_POST["name"])
    &&empty($_POST["comment"])
    &&!empty($_POST["edpass"])){

        $ednum=$_POST["ednum"];
        $edpass=$_POST["edpass"];
        
        $id=$ednum;
        
        $sql = 'SELECT * FROM '.$table.' WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        
	    foreach ($results as $row){
	        if($edpass==$row['pass']){

                $editnum=$row['id'];
                $editname=$row['name'];
                $editcomment=$row['comment'];
                $editpass=$row['pass'];
            }
	    }
	//編集機能開始
    }elseif(!empty($_POST["name"])
    &&!empty($_POST["comment"])
    &&!empty($_POST["editnum"])){
        
        $editnum=$_POST["editnum"];
        $editname=$_POST["name"];
        $editcomment=$_POST["comment"];
        $editpass=$_POST["pass"];
        
        $id=$editnum;
        $name=$editname;
        $comment=$editcomment;
        $date=date('Y/m/d H:i:s');
        $pass=$editpass;
        
        
        $sql = 'SELECT * FROM '.$table.' WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        
	    foreach ($results as $row){
            if($pass==$row['pass']){

                $id=$editnum;
        
                $sql = 'UPDATE '.$table.' 
                 SET name=:name,comment=:comment,date=:date,pass=:pass WHERE id=:id';
	            $stmt = $pdo->prepare($sql);
	            $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
	            $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
	            $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
	            $stmt -> bindParam(':date', $date, PDO::PARAM_STR);
	            $stmt -> bindParam(':pass', $pass, PDO::PARAM_STR);
    	
	            $stmt->execute();
            }
	    }
    }
        
    //削除機能開始
    if((!empty($_POST["delnum"]))
    &&(!empty($_POST["delpass"]))){
        
        $delnum=$_POST["delnum"];
        $delpass=$_POST["delpass"];
        
        $id = $delnum;
            
        $sql = 'SELECT * FROM '.$table.' WHERE id=:id ';
        $stmt = $pdo->prepare($sql);                  // ←差し替えるパラメータを含めて記述したSQLを準備し、
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ←その差し替えるパラメータの値を指定してから、
        $stmt->execute();                             // ←SQLを実行する。
        $results = $stmt->fetchAll(); 
        
            
	    foreach ($results as $row){
            if($delpass==$row['pass']){
                $sql = 'delete from '.$table.' where id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
	    }
    }
?>

    <p>焼肉屋で好きなメニューは何ですか?たくさん答えてください！</p>

    <form action="" method="post">
        <input type="text" name='name'
        placeholder="名前"
        value=  <?php
                if(!empty($ednum)){
                    echo $editname;
                }
                ?>>
        <br>
        <input type="text" name='comment' 
        placeholder="コメント"
        value=  <?php
                if(!empty($ednum)){
                    echo $editcomment;
                }
                ?>>
        <br>
        <input type="password" name='pass' 
        placeholder="パスワード" 
        autocomplete="new-password"
        value=  <?php
                if(!empty($ednum)){
                    echo $editpass;
                }
                ?>>
        <br>
        <input type="hidden" name="editnum"
        value=  <?php
                if(!empty($ednum)){
                    echo $editnum;
                }
                ?>>
        <input type="submit" name="submit">
        <br>
        <br>
        <input type="text" name="delnum" 
            placeholder="削除対象番号">
        <br>
        <input type="password" name="delpass" placeholder="パスワード">
        <br>
        <button type="submit">削除</button>
        <br>
        <input type="text" name="ednum"
            placeholder="編集対象番号">
        <br>
        <input type="password" name="edpass" placeholder="パスワード">
        <br>
        <button type="submit">編集</button>
        <br>
    </form>
    <p>**********投稿一覧************</p>
<?php
    $sql = 'SELECT * FROM '.$table;
	$stmt = $pdo->query($sql);
	$results = $stmt->fetchAll();
	foreach ($results as $row){
		//$rowの中にはテーブルのカラム名が入る
		echo $row['id'].',';
		echo $row['name'].',';
		echo $row['comment'].',';
		echo $row['date'].'<br>';
	echo "<hr>";
	}
	?>
</body>
</html>