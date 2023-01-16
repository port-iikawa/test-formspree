<?php
$err_msg = "";
if(isset($_POST['send'])) {

    /*
     * ここからバリデーション
     */
    $email = $_POST['email'] ?? '';
    //入力されたメールアドレスの全角英数字を半角に変換
    $email = mb_convert_kana($email, 'as');
    //スペースが混ざってたら除去
    $email = str_replace(" ", "", $email);
    //メールアドレスの形式を正規表現で確認
    if(preg_match("/^[a-zA-Z0-9_+-]+(.[a-zA-Z0-9_+-]+)*@([a-zA-Z0-9][a-zA-Z0-9-]*[a-zA-Z0-9]*\.)+[a-zA-Z]{2,}$/", $email) !== 1) {
        $err_msg = "メールアドレスが正しくありません";
    }
    
    /*
     * ここまでにエラーがなければ(メールアドレスが正しければ)、Formspreeにデータを送る
     */
    if( ! $err_msg){
        unset($_POST['send']);
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://formspree.io/f/xbjeldka',
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => $_POST,
            CURLOPT_HTTPHEADER => [
                'Accept: application/json'
            ],
        ]);

        //Formspreeに問題無くデータが送れた場合、jsonで情報が返却されるので、それをチェックして送信の成否を確認
        $send_result = json_decode(curl_exec($ch));
        if (empty($send_result->ok)) {
            $err_msg = "フォームが送信出来ませんでした。時間を置いて再度お試しください";
        } else {
            header("Location: ./complete.php");
        }
    }
}
/*
 * ここからはエンドユーザー向けのフロント部分
 */
?>
<!DOCTYPE html>
<html>

<head>
    <title>Formspreeテスト</title>
    <meta charset="utf-8" />
</head>


<body>
    <h1>Formspreeテスト</h1>
    <?php if($err_msg) echo "<p style='color:red;'>{$err_msg}</p>"; ?>
    <form action="./index.php" method="POST">
        <label>
             メールアドレス:
            <input type="email" name="email">
        </label>
        <br />
        <label>
            メッセージ:
            <textarea name="message"></textarea>
        </label>
        <br />
        <button type="submit" name="send" value="send">Send</button>
    </form>
</body>

</html>
