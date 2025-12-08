<?php

namespace App\Helpers;

class SeatHelper
{
    /**
     * Chuyển đổi loại ghế sang tiếng Việt có dấu
     * 
     * @param string $loai Loại ghế (thuong, vip, doi)
     * @return string Tên loại ghế tiếng Việt
     */
    public static function getSeatTypeName($loai)
    {
        $seatTypes = [
            'thuong' => 'Thường',
            'vip' => 'VIP',
            'doi' => 'Đôi',
        ];

        return $seatTypes[$loai] ?? ucfirst($loai);
    }
}
