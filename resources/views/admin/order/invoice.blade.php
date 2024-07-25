@php use App\Helpers\Helpers; @endphp
    <!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa Đơn Đơn Hàng</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f5f5f5; margin: 0; padding: 0;">
<div style="width: 100%; max-width: 800px; margin: 20px auto; ">
    <div style="border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 20px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; vertical-align: top; width: 50%;">
                <h2 style="font-size: 24px; margin: 0;">Hóa Đơn Bán Hàng</h2>
                <div style="text-align: left; font-size: 14px; color: #333;">
                    <p style="margin: 5px 0;"><span style="font-weight: bold;">Mã Đơn Hàng:</span>
                        #{{ $order?->order_code ?? '' }}</p>
                    <p style="margin: 5px 0;"><span style="font-weight: bold;">Trạng Thái:</span> {{ $status->label() }}
                    </p>
                    <p style="margin: 5px 0;"><span
                            style="font-weight: bold;">Tình Trạng Thanh Toán:</span> {{ $payment_status->labelText() }}
                    </p>
                    <p style="margin: 5px 0;"><span
                            style="font-weight: bold;">Mã Vận Chuyển:</span> {{ $order->delivery_code ?? '' }}</p>
                    <p style="margin: 5px 0;"><span
                            style="font-weight: bold;">Số Tiền:</span> {{ Helpers::formatVietnameseCurrency($order?->amount ?? null) }}
                    </p>
                </div>
            </div>
            <div style="display: table-cell; vertical-align: top; width: 50%; text-align: right;">
                <img src="{{ asset('images/dragon-phone-logo-5.png') }}" alt="Logo"
                     style="max-height: 50px; margin-bottom: 10px;">
                <div style="text-align: right; font-size: 14px; color: #333;">
                    <p style="margin: 5px 0;">Số Điện Thoại: 0399898559</p>
                    <p style="margin: 5px 0;">Địa Chỉ: Mỹ Đình 2, Nam Từ Liêm, Hà Nội</p>
                    <p style="margin: 5px 0;">Email: longchau241101@gmail.com</p>
                    <p style="margin: 5px 0;"><a href="{{ config('app.url') }}" target="_blank">{{ config('app.url') }}</a></p>
                </div>
            </div>
        </div>
    </div>

    <div style="display: table; width: 100%;">
        <div style="display: table-cell; width: 50%; padding: 10px;">
            <h3 style="font-size: 20px; margin-top: 0; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Thông Tin
                Khách Hàng</h3>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Tên:</span> {{ $order?->user?->name ?? '' }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Email:</span> {{ $order?->user?->email ?? '' }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Điện Thoại:</span>{{ $order?->user?->phone ?? '' }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Địa Chỉ:</span> {{ $order?->user?->address ?? '' }}
            </p>
        </div>
        <div style="display: table-cell; width: 50%; padding: 10px;">
            <h3 style="font-size: 20px; margin-top: 0; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Thông Tin
                Giao Hàng</h3>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Tên Người Nhận:</span> {{ $order?->name ?? '' }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Điện Thoại Người Nhận:</span> {{ $order?->email ?? '' }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Điện Thoại Người Nhận:</span> {{ $order?->phone ?? '' }}</p>
            <p style="margin: 5px 0;"><span style="font-weight: bold;">Địa Chỉ Giao Hàng:</span> {{ $order?->address ?? '' }}</p>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <h3 style="font-size: 20px; margin-top: 0; border-bottom: 2px solid #ddd; padding-bottom: 5px;">Sản Phẩm</h3>
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
            <tr>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Hình Ảnh</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Tên</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Màu</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Số Lượng</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Giá</th>
                <th style="border: 1px solid #ddd; padding: 8px; background-color: #f5f5f5;">Tổng</th>
            </tr>
            </thead>
            <tbody>
            @foreach($order?->order_details ?? [] as $orderDetail)
                <tr>
                    <td style="border: 1px solid #ddd; padding: 8px;">
                        <img
                            src="{{ $orderDetail?->product_detail?->product_image_urls[0] ?? '' }}"
                            class="product-image" alt="Hình Ảnh Sản Phẩm" style="width: 50px;">
                    </td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $orderDetail?->product_detail?->product?->name ?? '' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $orderDetail?->product_detail?->color ?? '' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ $orderDetail?->quantity ?? '' }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ Helpers::formatVietnameseCurrency($orderDetail?->price ?? null) }}</td>
                    <td style="border: 1px solid #ddd; padding: 8px;">{{ Helpers::formatVietnameseCurrency($orderDetail?->price * $orderDetail?->quantity) }}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr class="total-row">
                <th colspan="5" style="border: 1px solid #ddd; padding-top: 10px; text-align: right;">Tổng Tiền:
                </th>
                <td style="border: 1px solid #ddd; padding: 8px; text-align: right;">1,000,000 VND</td>
            </tr>
            </tfoot>
        </table>
    </div>

    <div
        style="text-align: center; margin-top: 30px; font-size: 16px; color: #333; border-top: 1px solid #ddd; padding-top: 10px;">
        <p>Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi!</p>
    </div>
</div>
<script>
    window.print();
</script>
</body>
</html>
