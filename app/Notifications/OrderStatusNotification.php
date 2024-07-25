<?php

namespace App\Notifications;

use App\Enums\OrderStatus;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderStatusNotification extends Notification
{
    use Queueable;

    protected Order $order;
    protected OrderStatus $status;

    /**
     * @param Order $order
     * @param OrderStatus $status
     */
    public function __construct(Order $order, OrderStatus $status)
    {
        $this->order = $order;
        $this->status = $status;
    }


    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $subject = '';
        $line = '';

        switch ($this->status) {
            case OrderStatus::Pending:
                $subject = 'Đơn hàng mới đang chờ xử lý';
                $line = 'Có một đơn hàng mới đang chờ xử lý.';
                break;

            case OrderStatus::Confirmed:
                $subject = 'Đơn hàng đã được xác nhận';
                $line = 'Đơn hàng của bạn đã được xác nhận.';
                break;

            case OrderStatus::Delivery:
                $subject = 'Đơn hàng đang vận chuyển';
                $line = 'Đơn hàng của bạn đang trên đường vận chuyển.';
                break;

            case OrderStatus::Done:
                $subject = 'Đơn hàng đã hoàn thành';
                $line = 'Đơn hàng của bạn đã hoàn thành.';
                break;

            case OrderStatus::Cancelled:
                $subject = 'Đơn hàng đã bị hủy';
                $line = 'Đơn hàng của bạn đã bị hủy.';
                break;
        }

        if ($notifiable->admin) {
            $url = url('/admin/order/' . $this->order->id . '/show');
        } else {
            $url = url('/order/' . $this->order->id);
        }
        return (new MailMessage)
            ->subject($subject)
            ->view('emails.test');
    }
}
