<?php
    $input = file_get_contents('php://input');
    if ($input=="")
        return;
    $json = json_decode($input, true);
    
    if (!isset($json['User']) || !isset($json['Pass']))
    {
        echo "Error !";
        return;
    }
    $arr = [];
    $arr['er'] = "";
    include "conn_sql.php";
    $user = strtolower($json['User']);
    $pass = md5($json['Pass']);
    
    if (!c_infor($user, $pass))
    {
        $arr['er'] = "Vui lòng nhập lại thông tin !";
        echo json_encode($arr);
        return;
    }

    $noidung = "SELECT * FROM account WHERE User = '$user' AND Pass = '$pass'";
    $result = mysqli_query($conn, $noidung);
    $k = mysqli_fetch_row($result);
    
    if ($k != 0)
    {
        $arr["Type"] = $k[2];
        $arr["Name"] = $k[3];
        $arr["MaSinhVien"] = $k[4];
        $MaSinhVien = $k[4];

        // Sách đã mượn

        $arr["DaMuon"] = [];

        $noidung = "SELECT * FROM muonsach WHERE MaSinhVien = '$MaSinhVien'";
        $result = mysqli_query($conn, $noidung);
        $k = mysqli_fetch_row($result);
        if ($k != 0)
        {
            while ($res = $k)
            {
                $q = [];
                $q['MaSach'] = $res[0];
                $q['NgayMuon'] = daongay($res[1]);
                $q['NgayTra'] = daongay($res[2]);
                $q['TinhTrang'] = $res[3];
                $noidung2 = "SELECT Name FROM thongtin WHERE MaSach = '".$q['MaSach']."'";
                $result2 = mysqli_query($conn, $noidung2);
                $k2 = mysqli_fetch_row($result2);
                if ($k2 != 0)
                {
                    $q['Name'] = $k2[0];
                }
                else
                {
                    echo "Có lỗi xảy ra !";
                    return;
                }
                array_push($arr["DaMuon"], $q);
                $k = mysqli_fetch_row($result);
            }
        }
        
        echo json_encode($arr);
        return;
    }

    $arr['er'] = "Tài khoản hoặc mật khẩu không chính xác !";
    echo json_encode($arr);
    return;

    function c_infor($user, $pass)
    {
        if (KyTuDacBiet($user))
            return 0;
        if ($user == "" || $pass == "")
            return 0;
        return 1;
    }

    function KyTuDacBiet($str)
    {
        return preg_match('/[\'^£$%&*()}{@#~?><>,|=_+¬-]/', $str);
    }

    function daongay($str)
    {
        return date("d-m-Y", strtotime($str)); 
    }
?>