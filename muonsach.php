<?php
    $input = file_get_contents('php://input');
    if ($input=="")
        return;
    $json = json_decode($input, true);

    if (!isset($json['MaSach']) || !isset($json['NgayMuon']) || !isset($json['NgayTra'])|| !isset($json['User']) || !isset($json['MaSinhVien']))
    {
        echo "Vui lòng kiểm tra lại thông tin !";
        return;
    }

    $MaSach = $json['MaSach'];
    $NgayMuon = $json['NgayMuon'];
    $NgayTra = $json['NgayTra'];
    $User = $json['User'];
    $MaSinhVien = $json['MaSinhVien'];

    $arr1 = explode("-", $NgayMuon);
    $arr2 = explode("-", $NgayTra);

    $NgayMuon = daongay($NgayMuon);
    $NgayTra = daongay($NgayTra);

    if (!isset($arr1[0]) || !isset($arr1[1]) || !isset($arr1[2]) || !isset($arr2[0]) || !isset($arr2[1]) || !isset($arr2[2]))
    {
        echo "Nhập lại ngày theo định dạng ngày-tháng-năm !";
        return;
    }

    if (checkdate( intval($arr1[1]), intval($arr1[0]), intval($arr1[2]) ) != 1)
    {
        echo "Ngày mượn sách không hợp lệ !";
        return;
    }

    if (checkdate( intval($arr2[1]), intval($arr2[0]), intval($arr2[2]) ) != 1)
    {
        echo "Ngày trả sách không hợp lệ !";
        return;
    }

    $first_date = strtotime($NgayMuon);
    $second_date = strtotime($NgayTra);
    $datediff = $first_date - $second_date;
    if (floor($datediff / (60*60*24) > 0))
    {
        echo "Nhập lại ngày mượn và ngày trả sách !";
        return;
    }

    include "conn_sql.php";
    $user = strtolower($User);

    if ($user == "")
    {
        echo "Vui lòng kiểm tra lại thông tin !";
        return;
    }
    $noidung = "SELECT * FROM thongtin WHERE MaSach = '$MaSach'";
    $result = mysqli_query($conn, $noidung);
    $k = mysqli_fetch_row($result);
    if ($k != 0)
    {
        $noidung = "SELECT * FROM account WHERE MaSinhVien = '$MaSinhVien'";
        $result = mysqli_query($conn, $noidung);
        $k = mysqli_fetch_row($result);
        if ($k != 0)
        {
            $noidung = "SELECT * FROM muonsach WHERE MaSinhVien = '$MaSinhVien' AND MaSach = '$MaSach' AND TinhTrang = '0'";
            $result = mysqli_query($conn, $noidung);
            $k = mysqli_fetch_row($result);
            if ($k != 0)
            {
                echo "Sách đã được mượn không thể thêm !";
                return;
            }
            $noidung = "INSERT INTO muonsach VALUES ('$MaSach', '$NgayMuon', '$NgayTra', '0', '$user', '$MaSinhVien')";

            $result = mysqli_query($conn, $noidung);
        
            if ($result)
            {
                echo "Xong !";
                return;
            }
        
            echo "Vui lòng kiểm tra lại thông tin !";
            return;
        }

        echo "Mã sinh viên không chính xác !";
        return;
    }
    echo "Mã sách không chính xác !";

    function daongay($str)
    {
        return date("Y-m-d", strtotime($str)); 
    }
?>