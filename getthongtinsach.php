<?php
    $input = file_get_contents('php://input');
    if ($input == "")
        return;
    $json = json_decode($input, true);

    if (!isset($json['MaSach']) || !isset($json['User']))
    {
        echo "Vui lòng kiểm tra lại thông tin !";
        return;
    }
    include "conn_sql.php";

    $MaSach = $json['MaSach'];
    $User = $json['User'];

    $kq = [];
    $kq["er"] = "";

    $noidung = "SELECT * FROM account WHERE User = '$User' AND Type = '1'";
    $result = mysqli_query($conn, $noidung);
    $k = mysqli_fetch_row($result);
    if ($k != 0)
    {
        $noidung = "SELECT * FROM thongtin WHERE MaSach = '$MaSach' LIMIT 1";
        $result = mysqli_query($conn, $noidung);
        $k = mysqli_fetch_row($result);
        if ($k != 0)
        {
            $kq['Name'] = $k[1];
            $kq['SoLuong'] = $k[2];
            $kq['NhaXuatBan'] = $k[3];
            $kq['TacGia'] = $k[4];
            $kq['NgayMua'] = daongay($k[5]);
            echo json_encode($kq);
            return;
        }
        $kq["er"] = "Mã sách không chính xác !";
        echo json_encode($kq);
        return;
    }
    $kq["er"] = "Không đủ quyền truy xuất !";
    echo json_encode($kq);

    function daongay($str)
    {
        return date("d-m-Y", strtotime($str)); 
    }


?>