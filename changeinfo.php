<?php

    $input = file_get_contents('php://input');
    if ($input=="")
        return;
    $json = json_decode($input, true);

    include "conn_sql.php";

    if (isset($json['Name']) && isset($json['MaSinhVien']) && isset($json['User']))
    {
        $user = $json['User'];
        $Name = $json['Name'];
        $MaSinhVien = $json['MaSinhVien'];

        if ( $user == "" || $Name == "" || $MaSinhVien == "")
        {
            echo "Vui lòng nhập đủ thông tin !";
            return;
        }

        $noidung = "SELECT * FROM account WHERE User = '$user' AND Name = '$Name' AND MaSinhVien = '$MaSinhVien'";
        $result = mysqli_query($conn, $noidung);
        $k = mysqli_fetch_row($result);
        if ($k != 0)
        {
            echo "Thông tin chưa thay đổi !";
            return;
        }
        else
        {
            $noidung = "SELECT * FROM account WHERE MaSinhVien = '$MaSinhVien' AND User != '$user'";
            $result = mysqli_query($conn, $noidung);
            $k = mysqli_fetch_row($result);
            if ($k != 0)
            {
                echo "Mã sinh viên đã tồn tại !";
                return;
            }
        }

        
        
        $noidung = "UPDATE account SET Name = '$Name', MaSinhVien = '$MaSinhVien' WHERE User = '$user'";
        $result = mysqli_query($conn, $noidung);
        if ($result)
        {
            echo "Đổi thông tin thành công !";
            return;
        }
        else echo "Có lỗi xảy ra !";
        return;
    }
    else echo "Có lỗi xảy ra !";
    
?>