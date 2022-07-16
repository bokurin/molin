<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>
<body>
    <b>朝型か夜型かを教えてください！</b>
<?php
     //DB接続設定
     $dsn = 'データベース名';
     $user = 'ユーザー名';
     $password = 'パスワード';
     $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

     $date=date("Y/m/d H:i:s"); //日時を定義
        //編集対象番号と編集用パスワードが存在するか確認
        if(!empty($_POST["edi"]) && !empty($_POST["epsw"])){
            //すべての行に対して編集用のパスワードが一致するか確認
            $sql  = "SELECT * FROM mis5_1";
            $stmt = $pdo->query($sql);
            $rows = $stmt->fetchAll();
            foreach ($rows as $r){
                //$rowの中にはテーブルのカラム名が入る
                if($_POST["edi"]==$r['id'] && $_POST["epsw"]==$r['pass']){
                    $edi=$_POST["edi"];
                }
            }
        }else{
            $edi="";
        }
        if($edi==""){
            $sub="送信";
            $na="";
            $co="";
            $pa="";
        }else{
            $sub="更新";
            //各行を抽出
            $stmt = $pdo->query("SELECT * FROM mis5_1");
            $rows = $stmt->fetchAll();
            foreach($rows as $r){
                if($r['id']==$edi){
                    $na=$r['name'];
                    $co=$r['comment'];
                    $pa=$r['pass'];
                }
            }
        }
?>
    <form action="" method="post">
        <input type="hidden" name="enum" value="<?php echo $edi ?>">
        <form action="" method="post">
        <input type="text" name="namae" value="<?php echo $na ?>" placeholder="名前">
        <br>
        <textarea cols="45" rows="5" wrap="soft"" name="str" placeholder="コメント"><?php echo $co ?></textarea>
        <br>
        <input type="text" name="psw" value="<?php echo $pa ?>" placeholder="パスワード">
        <input type="submit" name="submit" value="<?php echo $sub ?>">
        <br>
        <br>
        <input type="text" name="del" placeholder="削除番号">
        <br>
        <input type="text" name="dpsw" placeholder="パスワード">
        <input type="submit" name="delsub" value="削除">
        <br>
        <br>
        <input type="text" name="edi" placeholder="編集番号">
        <br>
        <input type="text" name="epsw" placeholder="パスワード">
        <input type="submit" name="edisub" value="編集">
    </form>
<?php
     //保留された編集対象番号が存在するか判断
     if(!empty($_POST["enum"])){
        $name=$_POST["namae"]; //名前
        $comment=$_POST["str"]; //コメント
        $pass=$_POST["psw"]; //パスワード
        //各行を抽出
        $stmt = $pdo->query("SELECT * FROM mis5_1");
        $rows = $stmt->fetchAll();
        //各行について繰り返す
        foreach($rows as $r){
            $id=$r['id'];
            if($_POST["enum"]==$id){
                //idが編集番号と一致した場合その行を更新 
                $sql='UPDATE mis5_1 SET name=:name, comment=:comment, date=:date, pass=:pass WHERE id=:id';
                $stmt=$pdo->prepare($sql);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                $stmt->bindParam(':date', $date, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
    //コメントと名前、パスワードが空かどうかを判断
    elseif(!empty($_POST["str"]) && !empty($_POST["namae"]) && !empty($_POST["psw"])){
         $comment=$_POST["str"]; //送信されたコメントを取得
         $name=$_POST["namae"]; //送信された名前を取得
         $pass=$_POST["psw"]; //送信されたパスワードを取得
         //データベースに新たな投稿を追記
         $sql = $pdo -> prepare("INSERT INTO mis5_1 (name, comment, date, pass) VALUES (:name, :comment, :date, :pass)");
         $sql -> bindParam(':name', $name, PDO::PARAM_STR);
         $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
         $sql -> bindParam(':pass', $pass, PDO::PARAM_STR);
         $sql -> bindParam(':date', $date, PDO::PARAM_STR);
         $sql -> execute();
    }
    //コメントと名前が入力されずかつ削除番号と削除用のパスワードが入力された場合にその投稿番号の行を削除
    elseif(!empty($_POST["del"]) && !empty($_POST["dpsw"])){
        $del=$_POST["del"];//送信された削除番号を取得
        $dpsw=$_POST["dpsw"];//送信された削除用のパスワードを取得
        //各行を抽出
        $stmt = $pdo->query("SELECT * FROM mis5_1");
        $rows = $stmt->fetchAll();
        //各行について繰り返す
        foreach($rows as $r){
            $id=$r['id']; //投稿されていた番号
            $pass=$r['pass']; //投稿されていたパスワード
            //番号とパスワードが一致したときに削除
            if($del==$id && $dpsw==$pass){
                $sql = 'DELETE FROM mis5_1 WHERE id=:id';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }
    //各行を抽出
    $stmt = $pdo->query("SELECT * FROM mis5_1");
    $rows = $stmt->fetchAll();
    //各行を表示
    foreach($rows as $r){
        echo "<br><br>";
        echo $r['id']."<br>";
        echo $r['name']."<br>";
        echo nl2br($r['comment'])."<br>";
        echo $r['date'];
    }
?>
</body>
</html>