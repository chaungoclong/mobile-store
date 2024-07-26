<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private Order $order;
    private bool $isAdmin;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Order $order, bool $isAdmin = false)
    {
        $this->order = $order;
        $this->isAdmin = $isAdmin;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $subject = $this->getSubject();
        $message = $this->getMessage();

        return (new MailMessage)
            ->subject($subject)
            ->line($message)
            ->action('Xem chi tiết đơn hàng', $this->getOrderUrl());
    }

    private function getOrderUrl(): string
    {
        if ($this->isAdmin) {
            return route('admin.order.show', ['id' => $this->order->getKey()]);
        }

        return route('order_page', ['id' => $this->order->getKey()]);
    }

    private function getSubject(): string
    {
        $status = OrderStatus::tryFrom((int)$this->order->getAttribute('status'));

        return match ($status) {
            OrderStatus::Pending => $this->isAdmin
                ? '[Admin] Đơn Hàng Mới #' . ($this->order->order_code ?? '') . ' - Đang Chờ Xác Nhận'
                : '[Cửa Hàng] Xác Nhận Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Đang Chờ',
            OrderStatus::Confirmed => $this->isAdmin
                ? '[Admin] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Đã Xác Nhận, Chuẩn Bị Giao Hàng'
                : '[Cửa Hàng] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Đã Xác Nhận',
            OrderStatus::Delivery => $this->isAdmin
                ? '[Admin] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Đang Vận Chuyển, Theo Dõi Tiến Trình'
                : '[Cửa Hàng] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Đang Vận Chuyển',
            OrderStatus::Done => $this->isAdmin
                ? '[Admin] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Hoàn Thành, Giao Hàng Thành Công'
                : '[Cửa Hàng] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Hoàn Thành',
            OrderStatus::Cancelled => $this->isAdmin
                ? '[Admin] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Đã Hủy, Kiểm Tra Lý Do'
                : '[Cửa Hàng] Đơn Hàng #' . ($this->order->order_code ?? '') . ' - Đã Hủy',
            default => '[Cửa Hàng] Đơn Hàng #' . ($this->order->order_code ?? ''),
        };
    }

    private function getMessage(): string
    {
        $status = OrderStatus::tryFrom((int)$this->order->getAttribute('status'));

        return match ($status) {
            OrderStatus::Pending => $this->isAdmin
                ? 'Vui lòng kiểm tra và xác nhận đơn hàng này.'
                : 'Cảm ơn bạn đã đặt hàng! Đơn hàng của bạn đang chờ xác nhận.',
            OrderStatus::Confirmed => $this->isAdmin
                ? 'Đơn hàng đã được xác nhận. Bắt đầu chuẩn bị và đóng gói.'
                : 'Đơn hàng của bạn đã được xác nhận và đang được chuẩn bị.',
            OrderStatus::Delivery => $this->isAdmin
                ? 'Đơn hàng đang được vận chuyển. Theo dõi tiến trình giao hàng.'
                : 'Đơn hàng của bạn đang được vận chuyển. Vui lòng chờ.',
            OrderStatus::Done => $this->isAdmin
                ? 'Đơn hàng đã hoàn thành và giao hàng thành công.'
                : 'Đơn hàng của bạn đã hoàn thành. Cảm ơn bạn đã mua sắm!',
            OrderStatus::Cancelled => $this->isAdmin
                ? 'Đơn hàng đã bị hủy. Kiểm tra lý do và xử lý nếu cần.'
                : 'Đơn hàng của bạn đã bị hủy. Nếu bạn có bất kỳ câu hỏi nào, vui lòng liên hệ với chúng tôi.',
            default => '',
        };
    }
}
