<?php
    $input = file_get_contents('php://input');
    if ($input == "")
        return;
    $json = json_decode($input, true);

    if (!isset($json['MaSach']) || !isset($json['Name']) || !isset($json['SoLuong']) || !isset($json['NhaXuatBan']) || !isset($json['TacGia']) || !isset($json['NgayMua']))
    {
        echo "Vui lòng kiểm tra lại thông tin !";
        return;
    }
    $MaSach = $json['MaSach'];
    $Name = $json['Name'];
    $SoLuong = $json['SoLuong'];
    $NhaXuatBan = $json['NhaXuatBan'];
    $TacGia = $json['TacGia'];
    $NgayMua = $json['NgayMua'];
    $arr = explode("-", $NgayMua);

    $NgayMua = daongay($NgayMua);
    if (!isset($arr[0]) || !isset($arr[1]) || !isset($arr[2]))
    {
        echo "Nhập lại ngày theo định dạng ngày-tháng-năm !";
        return;
    }

    if (checkdate( intval($arr[1]), intval($arr[0]), intval($arr[2]) ) != 1)
    {
        echo "Ngày mua sách không hợp lệ !";
        return;
    }
        

    include "conn_sql.php";

    if ($MaSach == "" || $Name == "" || $SoLuong == "" || $NhaXuatBan == "" || $TacGia == "" || $NgayMua == "")
    {
        echo "Thiếu thông tin !";
        return;
    }

    $noidung = "SELECT * FROM thongtin WHERE MaSach = '$MaSach'";
    $result = mysqli_query($conn, $noidung);
    $k = mysqli_fetch_row($result);
    if ($k != 0)
    {
        echo "Đã có sách này !";
        return;
    }

    $noidung = "INSERT INTO thongtin VALUES('$MaSach', '$Name', '$SoLuong', '$NhaXuatBan', '$TacGia', '$NgayMua')";

    $result = mysqli_query($conn, $noidung);
    if ($result)
    {
        echo "Thêm sách thành công !";
        return;
    }
    else echo "Không thể thêm sách !";

    function daongay($str)
    {
        return date("Y-m-d", strtotime($str)); 
    }
?>