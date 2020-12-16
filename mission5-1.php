<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_5</title>
</head>
<body>
  <?php
  //エラー表示
  ini_set('display_errors', "On");
  //データベース接続
  $user = 'ユーザー名';
  $password = 'パスワード';
  $dsn = 'データベース名';
  try {
    $dbh = new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
  } catch (PDOException $e) {
    echo '接続失敗'.$e->getMessage();
    exit;
  };
  //変数準備
  $editNumber = "";
  $editName = "";
  $editComment ="";
  $date = "";

  // //テーブル作成(mission4-2)
  // $sql = "CREATE TABLE IF NOT EXISTS tbtest5
  //      "."("."id INT AUTO_INCREMENT PRIMARY KEY,"
  //      ."name char(32),"."comment TEXT,"."pass char(32),"."date datetime".");";
  // $stmt = $dbh->query($sql);
  // //テーブルを表示(mission4-3)
  // $sql = "SHOW TABLES";
  // $result = $dbh->query($sql);
  // //順にテーブルを取り出す
  // foreach ($result as $row){
  //     echo $row[0]."<br>";
  // }
  // echo "<hr>";

  //テーブルを表示(mission4-4)
  // $sql = "SHOW CREATE TABLE tbtest5";
  // $result = $dbh->query($sql);
  // //順にテーブルを取り出す
  // foreach ($result as $row){
  //     echo $row[1];
  // }
  // echo "<hr>";

  //データの入力(mission4-5)

  // // //テーブルを削除する(mission4-9)
  // $sql = "DROP TABLE tbtest5";
  // $stmt = $dbh->query($sql);
  
  //メソッドがポストの場合開始する
  if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //modeの部分で判断する
    if (array_key_exists("mode",$_POST)){
      //valueという変数にmodeを代入する
      $value = $_POST['mode'];
      //switch文でvalueに入る処理別にわける
      switch($value){
        //valueがinsertの場合
        case "insert":
          try {
            //投稿機能(名前とコメントが空じゃないとき)
            //パスワードが空の場合も投稿できないようにする(3-5)
            if (!empty($_POST['personal_name']) && !empty($_POST['comment'])
                              && !empty($_POST['password'])){
              //編集機能
              if (!empty($_POST['edit_post'])){
                $id = $_POST['edit_post'];
                $name = $_POST['personal_name'];
                $comment = $_POST['comment'];
                $nowTime = date("Y-m-d H:i:s", time());
                $sql = "UPDATE tbtest5 SET name=:name,comment=:comment,date=:date WHERE id=:id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(":name",$name,PDO::PARAM_STR);
                $stmt->bindValue(":comment",$comment,PDO::PARAM_STR);
                $stmt->bindValue(":id",$id,PDO::PARAM_INT);
                $stmt->bindValue(":date",$nowTime,PDO::PARAM_STR);
                $stmt->execute();
              //投稿機能
              } else {
                $sql = $dbh->prepare("INSERT INTO tbtest5 (name,comment,pass,date) VALUES (:name,:comment,:pass,:date)");
                $sql->bindParam(":name",$name,PDO::PARAM_STR);
                $sql->bindParam(":comment",$comment,PDO::PARAM_STR);
                $sql->bindParam(":pass",$pass,PDO::PARAM_STR);
                $sql->bindParam(":date",$nowTime,PDO::PARAM_STR);
                $name = $_POST['personal_name'];
                $comment = $_POST['comment'];
                $pass = $_POST['password'];
                $nowTime = date("Y-m-d H:i:s", time());
                $sql->execute();
              }
            }elseif(empty($_POST['personal_name']) && empty($_POST['comment'])) {
              echo '名前とコメントを入力してください';
            }elseif(empty($_POST['personal_name'])){
              echo '名前を入力してください';
            }elseif(empty($_POST['comment'])){
              echo 'コメントを入力してください';
            }      
          } catch (PDOException $e) {
            echo "投稿エラー:".$e->getMessage();
        
            exit;
          }
        break;
        //valueがdeleteの場合
        case "delete":
          try {
            //tbtest5テーブルを選択する
            $sql = "SELECT * FROM tbtest5";
            $stmt = $dbh->query($sql);
            $results = $stmt->fetchAll();
            //1行ずつ取り出す
            foreach ($results as $row){
              //削除機能
              //削除番号に該当するかつ、パスワードが正しい場合削除
              if ($row['id'] == $_POST['del_num'] && $row['pass'] == $_POST['del_pass']){
                $id = $_POST['del_num'];
                $sql = "delete from tbtest5 where id = :id";
                $stmt = $dbh->prepare($sql);
                $stmt->bindValue(":id",$id,PDO::PARAM_INT);
                $stmt->execute();
                break;
              //パスワードが間違っている場合
              } elseif ($row['pass'] != $_POST['del_pass']){
                continue;
              }
            }
          } catch (PDOException $e) {
            echo "削除エラー".$e->getMessage();
            exit;
          }
        break;
        //valueがeditの場合
        case "edit":
          try {
            //tbtest5テーブルを選択する
            $sql = "SELECT * FROM tbtest5";
            $stmt = $dbh->query($sql);
            $results = $stmt->fetchAll();
            //1行ずつ取り出す
            foreach ($results as $row){
              //編集選択機能
              //編集番号に該当するかつ、パスワードが正しい場合変数に代入
              if ($row["id"] == $_POST['edit_num'] && $row['pass'] == $_POST['edit_pass']){
                $editNumber = $row["id"];
                $editName = $row["name"];
                $editComment = $row["comment"];
                break;
              //パスワードが間違っている場合
              } else {
                continue;
              }
            }
          } catch (PDOException $e) {
            echo "編集エラー".$e->getMessage();
            exit;
          }
        break;
      }
    }
  }
  ?>
  <form method="post">
      <p>
      <input type="text" name="personal_name" maxLength="22" 
              placeholder="名前" value="<?php echo $editName; ?>"><br>
      <input type="text" name="comment" maxLength="22" placeholder="コメント"
              value="<?php echo $editComment; ?>"><br>
      <input type="hidden" name="edit_post" maxLength="22"
              value="<?php echo $editNumber; ?>">
      <input type="password" name="password" maxLength="22" placeholder="パスワード" required>
      <button type="submit" name="mode" value="insert">送信</button>
      </p>
  </form>
  <form method="post">
      <p>
      <input type="text" name="del_num" maxLength="22"
              placeholder="削除対象番号"><br>
      <input type="password" name="del_pass" maxLength="22" placeholder="パスワード" >
      <button type="submit" name="mode" value="delete">削除</button>
      </p>
  </form>
  <form method="post">
      <p>
      <input type="text" name="edit_num" maxLength="22"
              value="<?php echo $editNumber; ?>" placeholder="編集対象番号"><br>
      <input type="password" name="edit_pass" maxLength="22" placeholder="パスワード" required>
      <button type="submit" name="mode" value="edit">編集</button>
      </p>
  </form><hr>
  <?php
  //データを抽出し、表示する(mission4-6)
  $sql = "SELECT * FROM tbtest5";
  $stmt = $dbh->query($sql);
  $results = $stmt->fetchAll();
  foreach ($results as $row){
      echo $row["id"].",";
      echo $row["name"].",";
      echo $row["comment"].",";
      echo $row["pass"].",";
      echo $row["date"]."<br>";
  }
  ?>
</body>
</html>