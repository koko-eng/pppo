<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <title>mission_5-1</title>
</head>

<body>
    <?php
       // DB接続設定
       $dsn = 'データベース名';
       $user = 'ユーザ名';
       $password = 'パスワード';
       $pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

       //テーブル作成
       $sql = "CREATE TABLE IF NOT EXISTS tb_test"
       ." ("
       . "id INT AUTO_INCREMENT PRIMARY KEY,"
       . "name char(32),"
       . "comment TEXT,"
       . "password TEXT,"
       . "time TEXT"
       .");";
       $stmt = $pdo->query($sql);



    // エラーを画面に表示(1を0にすると画面上にはエラーは出ない)
    ini_set('display_errors', 1);

    if (!empty($_POST["edit_mode"])) {
        $edit_mode = $_POST["edit_mode"];
    } else {
        $edit_mode = false;
    }
    //エラー
    if (!empty($_POST["name"])) {
        $name = $_POST["name"];
    } else {
        $name = "";
    }

    if (!empty($_POST["comment"])) {
        $comment = $_POST["comment"];
    } else {
        $comment = "";
    }

    if (!empty($_POST["edit_write"])) {
        $edit_write = $_POST["edit_write"];
    } else {
        $edit_write = "";
    }
    if (!empty($_POST["edit_choice"])) {
        $edit_choice = $_POST["edit_choice"];
    } else {
        $edit_choice = "";
    }
    if (!empty($_POST["del_password"])) {
        $del_password = $_POST["del_password"];
    } else {
        $del_password = "";
    }
    if (!empty($_POST["a_password"])) {
        $a_password = $_POST["a_password"];
    } else {
        $a_password = "";
    }
    if (!empty($_POST["ed_password"])) {
        $ed_password = $_POST["ed_password"];
    } else {
        $ed_password = "";
    }

    $time = date("Y/m/d H:i:s");
    
	
            //該当番号のデータを抽出
    $sql = 'SELECT * FROM tb_test WHERE id=:id';
            $stmt = $pdo -> prepare($sql);
            $stmt -> bindParam(':id', $id1, PDO::PARAM_INT);
            $stmt -> execute();
            $result = $stmt -> fetch(PDO::FETCH_ASSOC);
     
            /*投稿番号の定義*/
    $sql = 'SELECT max(id) as id FROM tb_test';  //投稿番号はidからmax使ってとってくる
            $stmt = $pdo->query($sql);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $number = $result["id"];

            if(empty($number)){
                     $number = 1;
            } else{
                     $number++;
                    }

    //投稿機能

    if (!empty($_REQUEST["submit"]) && !$edit_mode && !empty($_POST['a_password'])&&(!empty($_POST["name"]))&&(!empty($_POST["comment"]))) {
       
        $sql = $pdo -> prepare("INSERT INTO tb_test (name, comment, password, time)
        VALUES (:name, :comment, :password, :time)");
        $sql -> bindParam(':name', $name, PDO::PARAM_STR);
	    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $sql -> bindParam(':password', $a_password, PDO::PARAM_STR);
        $sql -> bindParam(':time', $time, PDO::PARAM_STR);
        $sql -> execute();

     }

    //削除機能
    if ((isset($_REQUEST["del"])) && isset($_POST['del_password']) && isset($_POST['delete'])) {
        $id = $_POST['delete'];
        $del_password = $_POST["del_password"];
        $sql = 'delete from tb_test where id=:id and password=:password';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':password',$_POST["del_password"],PDO::PARAM_STR);
        $stmt->execute();        
    }


    



   //編集書き込み
    if ((isset($_REQUEST["submit"])) && $edit_mode && !empty($_POST["a_password"]) && isset($_POST['editNo'])) {
        $id = $_POST["editNo"];
        $ed_password = $_POST["a_password"];
        $edit_mode = false;
        $sql = 'UPDATE  tb_test SET name=:name, comment=:comment, password=:password, time=:time WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt -> bindParam(':name', $name, PDO::PARAM_STR);
	    $stmt -> bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt -> bindParam(':password', $a_password, PDO::PARAM_STR);
        $stmt -> bindParam(':time', $time, PDO::PARAM_STR);
        $stmt -> bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();   

        
    }




    //編集読み込み

    if ((isset($_REQUEST["edit"])) && !empty($_POST["ed_password"])) {
        $edit_choice = $_POST["edit_choice"];
        $ed_password = $_POST["ed_password"];
        $sql = 'SELECT * FROM  tb_test  WHERE id=:id and password=:password';
        $stmt = $pdo->prepare($sql);
		$stmt -> bindParam(':password', $ed_password, PDO::PARAM_STR);
		$stmt -> bindParam(':id', $edit_choice, PDO::PARAM_INT);
        $stmt->execute();
        $edit_mode = true;
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $editname=$result["name"];
        $editcomment=$result["comment"];
        $editpass=$result["password"];
        $editNo=$result["id"];
        }
        
    

    ?>

    <!--返信フォーム-->
    <form action="" method="post" enctype="multipart/form-data">
        <!--ファイルの指定-->
        <input type="text" name="name" placeholder="名前" value="<?php if (isset($editname)) {
                                                                    echo $editname;
                                                                } ?>"><br>
        <!--名前フォーム作成-->
        <input type="text" name="comment" placeholder="コメント" value="<?php if (isset($editcomment)) {
                                                                        echo $editcomment;
                                                                    } ?>"><br>
        <!--コメントフォーム作成-->
        <input type="password" name="a_password" placeholder="パスワード" value="<?php if (isset($editpass)) {
                                                                                echo $editpass;
                                                                            } ?>"><br>
        <!--passフォーム作成-->
        <input type="submit" name="submit" value="送信"><br>
        <!--送信ボタン作成-->
        <?php
        if (!empty($editname) && !empty($editcomment)) {
            echo '<input type="hidden" name="test" value="tset1">';
        }
        ?>

        <!--削除フォーム-->
        <input type="text" name="delete" placeholder="削除対象番号"><br>
        <!--削除フォーム作成-->
        <input type="password" name="del_password" placeholder="パスワード" value="<?php if (isset($editpass)) {
                                                                                    echo $editpass;
                                                                                } ?>"><br>
        <!--passフォーム作成-->
        <input type="submit" name="del" value="削除"><br>
        <!--削除ボタン作成-->

        <!--編集フォーム-->
        <input type="text" name="edit_choice" placeholder="編集対象番号"><br>
        <!--編集フォーム作成-->
        <input type="password" name="ed_password" placeholder="パスワード" value="<?php if (isset($editpass)) {
                                                                                    echo $editpass;
                                                                                } ?>"><br>
        <!--passフォーム作成-->
        <input type="submit" name="edit" value="編集"><br>
        <!--編集ボタン作成-->
        <input type="hidden" name="edit_mode" value="<?php echo $edit_mode; ?>"><br>
        <!--編集か新規か判定-->
        <input type="hidden" name="editNo" value="<?php echo $editNo; ?>"><br>
        <!--編集番号判定-->

    </form>


    <?php

    $sql = 'SELECT * FROM tb_test';
            $stmt = $pdo->query($sql);  // ←差し替えるパラメータを含めて記述したSQLを準備し、
            $results = $stmt->fetchAll(); // ←その差し替えるパラメータの値を指定してから、
            foreach ($results as $row){  // ←SQLを実行する。
 
        //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'].',';
            echo $row['time'].'<br>';
            echo "<hr>";
         }
 

    ?>
</body>

</html>