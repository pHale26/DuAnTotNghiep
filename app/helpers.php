<?php

if (! function_exists('format_currency')) {
    /**
     * Định dạng tiền tệ và chuyển đổi theo cấu hình cứng
     * Mặc định DB lưu giá VNĐ.
     */
    function format_currency($amount)
    {
        // Trả về định dạng VNĐ cố định
        return number_format($amount, 0, ',', '.').'đ';
    }
}
