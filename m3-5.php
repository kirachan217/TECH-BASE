<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>mission_3-5</title>
</head>
<body>

    <?php
    //ファイル名指定
    $filename = "mission_3-5.txt";
    
    //変数指定（投稿フォーム）
    $name = $_POST["name"];
    $comm = $_POST["comm"];
    $pass = $_POST["pass"];
    $first = $_POST["first"];//通常の投稿ボタン
    $second = $_POST["enumber"];//編集したときの投稿番号
    //変数指定（削除フォーム）
    $deleteNo = $_POST["deleteNo"];
    $deletePass = $_POST["deletePass"];
    $delete = $_POST["delete"];
    //変数指定（編集フォーム）
    $editNo = $_POST["editNo"];
    $editPass = $_POST["editPass"];
    $edit = $_POST["edit"];
    //日時データ取得
    $date = date("Y年m月d日 H:i:s");
    
    //一行ずつ配列にする
    $lines = file($filename,FILE_IGNORE_NEW_LINES);
    
    //ここから入力フォーム
    //名前とコメントとパスワードが入力されているときに
    if(isset($name) && isset($comm) && isset($pass)){
        //入力データ指定
        $str = $second."<>".$name."<>".$comm."<>".$date."<>".$pass."<>";
        if($second){
            //ファイルを上書きモードで開く…番号指定されてない行をコピーしたいから
            $fp = fopen($filename,"w");
            //すべての行を一行ずつ考える
            foreach($lines as $line){
                //行を分解
                $slist = explode("<>",$line);
                if((int)$slist[0] == $second){
                    $str = $second."<>".$name."<>".$comm."<>".$date."<>".$pass."<>";
                    fwrite($fp,$str."\n");
                }else{
                    $str = $slist[0]."<>".$slist[1]."<>".$slist[2]."<>".$slist[3]."<>".$slist[4]."<>";
                    fwrite($fp,$str."\n");
                }
            }
        }else{
            $last = end($lines);
            $count = (int)$last + 1;
            $last = reset($lines);
            $str = $count."<>".$name."<>".$comm."<>".$date."<>".$pass."<>";
            $fp = fopen($filename,"a");
            fwrite($fp,$str."\n");
            fclose($fp);
        }
        
    //ここから削除フォーム
    }elseif(isset($deleteNo)){
        //ファイルが存在するとき
        if(file_exists($filename)){
            $lines = file($filename,FILE_IGNORE_NEW_LINES);
            //ファイルを上書きモードで開く
            $fp = fopen($filename,"w");
            foreach($lines as $line){
                //行を分解
                $dellist = explode("<>",$line);
                
                //投稿番号と選択番号が間違っていたら
                if((int)$dellist[0] != $deleteNo){
                        $str = $dellist[0]."<>".$dellist[1]."<>".$dellist[2]."<>".$dellist[3]."<>".$dellist[4]."<>";
                        //そのまま書き込む
                        fwrite($fp, $str."\n");
                }
                
                //投稿番号と選択番号が合っていたら
                if((int)$dellist[0] == $deleteNo){
                        //パスワードが間違っていたら
                        if($deletePass != $dellist[4]){
                        $str = $dellist[0]."<>".$dellist[1]."<>".$dellist[2]."<>".$dellist[3]."<>".$dellist[4]."<>";
                        //そのまま書き込む
                        fwrite($fp, $str."\n");
                        //「パスポートが違います」と表示する
                        echo("パスワードが違います");
                        }
                }
            }
        }
    
    
    /*！10/26現在--10/27解決
    編集時、パスワードがあっているのに「間違っています」がecho
    投稿フォームの「パスワード」のみ表示される。
    「名前」「コメント」は空のまま。
    */
        
    //ここから編集フォーム
    }elseif(isset($editNo)){
            $enumber = "";
            $eName = "";
            $eComm = "";
            $newPass = "";
            if(file_exists($filename)){
            //ファイルが存在したら
                //$lines = file($filename,FILE_IGNORE_NEW_LINES);
                //ファイルを上書きモードで開く
                $fp = fopen($filename,"w");
                foreach($lines as $line){
                    //行を分解する
                    $editlist = explode("<>",$line);
                    
                    //投稿番号と選択番号が合っていたら
                    if((int)$editlist[0] == $editNo){
                        
                        //パスワードが合っていたら
                        if($editlist[4] == $editPass){
                            //それぞれ代入
                            $enumber = $editlist[0];//投稿番号(hidden)
                            $eName = $editlist[1];//名前
                            $eComm = $editlist[2];//コメント
                            $newPass = $editlist[4];
                        //パスワードが間違っていたら
                        }else{
                            echo "パスワードが違います";
                        }
                    }
                    $str = $editlist[0]."<>".$editlist[1]."<>".$editlist[2]."<>".$editlist[3]."<>".$editlist[4]."<>";
                    fwrite($fp, $str."\n");
                }
            }
        
    }
    
    /*完成させたい編集の仕組み
    
    *投稿番号を指定して、「編集」ボタンを押すと、その投稿内容が
    *投稿フォーム内に表示され、そこで編集をし、「投稿」ボタンを押すと
     編集された状態でテキストに保存される*/   
    ?>
    
    <p>入力フォーム</p>
    <form action="" method="post">
        <!-- valueに変数を指定して、編集ボタンが押されたときに
        テキストボックスに表示されるようにする-->
        <input type="text" name="name" 
            value="<?php echo "$eName"; ?>" placeholder="名前">
        <input type="text" name="comm" 
            value="<?php echo "$eComm"; ?>" placeholder="コメント">
        <input type="texe" name="pass" placeholder="パスワード"value ="<?php echo "$newPass"; ?>">
        <input type="submit" name="first" value="投稿">
        <input type ="hidden" name ="enumber"
            value="<?php echo "$enumber"; ?>">
    </form>
    
    <p>削除フォーム</p>
    <form action="" method="post">
        <input type = "number" name = "deleteNo" placeholder = "削除対象番号">
        <input type = "text" name = "deletePass" placeholder = "パスワード">
        <input type = "submit" name = "delete" value = "削除">
    </form>
    
    <p>編集フォーム</p>
    <form action="" method="post">
        <input type = "number" name = "editNo" placeholder = "編集対象番号">
        <input type = "text" name = "editPass" placeholder = "パスワード">
        <input type = "submit" name = "edit" value = "編集">
    </form>
    
    <?php
        if(file_exists($filename)){
        $lines = file($filename,FILE_IGNORE_NEW_LINES);
        foreach($lines as $line){
            
        $cut =explode("<>",$line);
        $count = $cut[0];
        $name = $cut[1];
        $comm = $cut[2];
        $date = $cut[3];
        $pass = $cut[4];
        echo $count." ".$name." ".$comm." ".$date." ".$pass. "<br>";
        }
    }
    ?>

    
</body>
</html>