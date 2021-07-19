<?php
    $input = file_get_contents('php://input');
    if ($input == "")
        return;
    $json = json_decode($input, true);

    if (!isset($json['MaSach']) || !isset($json['User']) || !isset($json['NgayTra']) || !isset($json['MaSinhVien']))
    {
        echo "Vui lòng kiểm tra lại thông tin !";
        return;
    }

    include "conn_sql.php";

    $MaSach = $json['MaSach'];
    $User = $json['User'];
    $NgayTra = $json['NgayTra'];
    $MaSinhVien = $json['MaSinhVien'];

    $arr = explode("-", $NgayTra);

    $NgayTra = daongay($NgayTra);
    if (!isset($arr[0]) || !isset($arr[1]) || !isset($arr[2]))
    {
        echo "Nhập lại ngày theo định dạng ngày-tháng-năm !";
        return;
    }

    if (checkdate( intval($arr[1]), intval($arr[0]), intval($arr[2]) ) != 1)
    {
        echo "Ngày trả sách không hợp lệ !";
        return;
    }

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
                    $hantra = $k[2];

                    $first_date = strtotime($hantra);
                    $second_date = strtotime($NgayTra);
                    $datediff = $first_date - $second_date;
                    if (floor($datediff / (60*60*24) >= 0))
                        $noidung = "UPDATE muonsach SET NgayTra = '$NgayTra' , TinhTrang = '1' WHERE MaSinhVien = '$MaSinhVien' AND MaSach = '$MaSach'";
                    else $noidung = "UPDATE muonsach SET NgayTra = '$NgayTra' , TinhTrang = '2' WHERE MaSinhVien = '$MaSinhVien' AND MaSach = '$MaSach'";
                    $result = mysqli_query($conn, $noidung);
                    if ($result)
                    {
                        if (floor($datediff / (60*60*24) < 0))
                        {
                            echo "Quá hạn ".abs(floor($datediff / (60*60*24)))." ngày !";
                            return;
                        }
                        echo "Đã trả sách đúng hạn !";
                        return;
                    }
                    echo "Có lỗi xảy ra !";
                    return;
                }
                echo "Không có trong danh sách mượn !";
                return;
            }
            echo "Mã sinh viên không chính xác !";
            return;
        }
        echo "Mã sách không chính xác !";
        return;
    }
    echo "Không đủ quyền truy cập !";

    function daongay($str)
    {
        return date("Y-m-d", strtotime($str)); 
    }
?>