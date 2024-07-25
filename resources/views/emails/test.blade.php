<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn Đơn Hàng</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0;">
<div style="width: 100%; max-width: 800px; margin: 20px auto; padding: 20px;">
    <div style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; vertical-align: top; width: 50%;">
                <h2 style="font-size: 24px; margin: 0;">Hóa Đơn Bán Hàng</h2>
                <div style="text-align: left; font-size: 14px; color: #333;">
                    <p style="margin: 5px 0;"><span style="font-weight: bold;">Mã Đơn Hàng:</span> #123456</p>
                    <p style="margin: 5px 0;"><span style="font-weight: bold;">Trạng Thái:</span> Đang Chờ</p>
                    <p style="margin: 5px 0;"><span style="font-weight: bold;">Tình Trạng Thanh Toán:</span> Đã Thanh Toán</p>
                    <p style="margin: 5px 0;"><span style="font-weight: bold;">Mã Vận Chuyển:</span> Chưa Có</p>
                    <p style="margin: 5px 0;"><span style="font-weight: bold;">Số Tiền:</span> 1,000,000 VND</p>
                </div>
            </div>
            <div style="display: table-cell; vertical-align: top; width: 50%; text-align: right;">
                <img src="{{ \App\Helpers\Helpers::imageToBase64(asset('images/dragon-phone-logo-5.png')) }}" alt="Logo" style="max-height: 50px; margin-bottom: 10px;">
                <div style="text-align: right; font-size: 14px; color: #333;">
                    <p style="margin: 5px 0;">Số Điện Thoại: 0123-456-789</p>
                    <p style="margin: 5px 0;">Địa Chỉ: 1234 Đường, Thành Phố, Quốc Gia</p>
                    <p style="margin: 5px 0;">Email: store@example.com</p>
                    <p style="margin: 5px 0;"><a href="http://www.example.com" target="_blank">www.example.com</a></p>
                </div>
            </div>
        </div>
    </div>

    <div style="display: table; width: 100%;">
        <div style="display: table-cell; width: 50%; padding: 10px;">
            <h3 style="font-size: 20px; margin-top: 0; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Thông Tin Khách Hàng</h3>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Tên:</span> John Doe</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Email:</span> john.doe@example.com</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Điện Thoại:</span> 123-456-7890</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Địa Chỉ:</span> 1234 Đường, Thành Phố, Quốc Gia</p>
        </div>
        <div style="display: table-cell; width: 50%; padding: 10px;">
            <h3 style="font-size: 20px; margin-top: 0; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Thông Tin Giao Hàng</h3>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Tên Người Nhận:</span> Jane Doe</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Điện Thoại Người Nhận:</span> 987-654-3210</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Địa Chỉ Giao Hàng:</span> 5678 Đường, Thành Phố, Quốc Gia</p>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <h3 style="font-size: 20px; margin-top: 0; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Sản Phẩm</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Hình Ảnh</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Tên</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Số Lượng</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Giá</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Tổng</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="border: 1px solid #ddd; padding: 8px;"><img src="path_to_image.jpg" alt="Hình Ảnh Sản Phẩm" style="max-width: 50px; max-height: 50px;"></td>
                <td style="border: 1px solid #ddd; padding: 8px;">Điện Thoại Model XYZ</td>
                <td style="border: 1px solid #ddd; padding: 8px;">1</td>
                <td style="border: 1px solid #ddd; padding: 8px;">1,000,000 VND</td>
                <td style="border: 1px solid #ddd; padding: 8px;">1,000,000 VND</td>
            </tr>
            <!-- Repeat for other products -->
            </tbody>
            <tfoot>
            <tr class="total-row">
                <th colspan="4" style="border-top: 2px solid #ddd; padding-top: 10px; text-align: right;">Tổng Tiền:</th>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">1,000,000 VND</td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div style="text-align: center; margin-top: 30px; font-size: 16px; color: #333; border-top: 1px solid #ddd; padding-top: 10px;">
        <p>Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi!</p>
    </div>
</div>
<script>
    window.print();
</script>
</body>
</html>
