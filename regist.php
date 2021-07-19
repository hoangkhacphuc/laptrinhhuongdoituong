<?php
    $input = file_get_contents('php://input');
    if ($input=="")
        return;
    $json = json_decode($input, true);

    if (!isset($json['User']) || !isset($json['Pass']) || !isset($json['rePass']))
    {
        echo "Error !";
        return;
    }
    include "conn_sql.php";

    $user = strtolower($json['User']);
    $pass = $json['Pass'];
    $repass = $json['rePass'];

    if (!c_infor($user, $pass, $repass))
    {
        echo "Vui lòng nhập lại thông tin !";
        return;
    }
    $pass = md5($pass);

    $noidung = "SELECT * FROM account WHERE User = '$user'";
    $result = mysqli_query($conn, $noidung);
    $k = mysqli_fetch_row($result);
    if ($k != 0)
    {
        echo "Tài khoản đã tồn tại !";
        return;
    }

    $noidung = "INSERT INTO account VALUES('$user', '$pass', '2','','','','','')";
    $result = mysqli_query($conn, $noidung);
    if ($result)
    {
        echo "Đăng ký thành công !";
        return;
    }
    
    function c_infor($user, $pass, $repass)
    {
        if (KyTuDacBiet($user))
            return 0;
        if ($user == "" || $pass == "" || $repass == "")
            return 0;
        if (strlen($user) < 5 || strlen($user) > 20 || strlen($pass) < 8 || strlen($pass) > 20 || strlen($repass) < 8 || strlen($repass) > 20)
            return 0;
        if ($pass != $repass)
            return 0;
        return 1;
    }

    function KyTuDacBiet($str)
    {
        return preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $str);
    }
?>