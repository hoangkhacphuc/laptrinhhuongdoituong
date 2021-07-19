<?php

    $input = file_get_contents('php://input');
    if ($input=="")
        return;
    $json = json_decode($input, true);

    include "conn_sql.php";

    if (isset($json['p1']) && isset($json['p2']) && isset($json['p3']) && isset($json['User']))
    {

        if (strlen($json['p2']) < 8)
        {
            echo "Mật khẩu quá ngắn !";
            return;
        }
        $user = $json['User'];
        $pass = md5($json['p1']);
        $newpass = md5($json['p2']);
        $renewpass = md5($json['p3']);

        if ( $pass == "" || $newpass == "" || $renewpass == "")
        {
            echo "Vui lòng nhập đủ thông tin !";
            return;
        }

        if (strlen($newpass) > 500)
        {
            echo "Mật khẩu quá dài !";
            return;
        }
        
        if ($newpass == $pass)
        {
            echo "Mật khẩu cũ và mới giống nhau !";
            return;
        }

        if ($newpass != $renewpass)
        {
            echo "Nhập lại mật khẩu không chính xác !";
            return;
        }

        $noidung = "SELECT * FROM account WHERE User = '$user' AND Pass = '$pass'";
        $result = mysqli_query($conn, $noidung);
        $k = mysqli_fetch_row($result);
        if (!($k != 0))
        {
            echo "Mật khẩu cũ không chính xác !";
            return;
        }
        
        $noidung = "UPDATE account SET Pass = '$newpass' WHERE User = '$user'";
        $result = mysqli_query($conn, $noidung);
        if ($result)
        {
            echo "Đổi mật khẩu thành công !";
            return;
        }
        else echo "Có lỗi xảy ra !";
        return;
    }
    else echo "Có lỗi xảy ra !";
    
?>