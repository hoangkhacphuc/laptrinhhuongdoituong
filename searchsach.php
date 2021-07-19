<?php
    $input = file_get_contents('php://input');
    if ($input == "")
        return;
    $json = json_decode($input, true);

    if (!isset($json['info']))
    {
        echo "Vui lòng kiểm tra lại thông tin !";
        return;
    }
    include "conn_sql.php";

    $info = $json['info'];

    $kq = [];
    $kq["er"] = "";

    $noidung = "SELECT * FROM thongtin WHERE Name LIKE '%$info%' OR MaSach LIKE '%$info%' OR TacGia LIKE '%$info%'";
    $result = mysqli_query($conn, $noidung);
    $res = mysqli_fetch_row($result);
    while ($k = $res)
    {
        $q = [];
        $q['MaSach'] = $k[0];
        $q['Name'] = $k[1];
        $q['SoLuong'] = $k[2];
        $q['NhaXuatBan'] = $k[3];
        $q['TacGia'] = $k[4];
        $q['NgayMua'] = daongay($k[5]);
        $ms =  $k[0];

        $noidung = "SELECT count(MaSach) FROM muonsach WHERE MaSach = '$ms'";
        $result2 = mysqli_query($conn, $noidung);
        $z = mysqli_fetch_row($result2);
        if ($z != 0)
        {
            $q['Con'] = $q['SoLuong'] - $z[0];
        }
        array_push($kq, $q);
        $res = mysqli_fetch_row($result);
    }
    if ($kq != [])
    {
        echo json_encode($kq);
        return;
    }
    $kq["er"] = "Không tìm thấy dữ liệu !";
    echo json_encode($kq);
    return;

    function daongay($str)
    {
        return date("d-m-Y", strtotime($str)); 
    }


?>