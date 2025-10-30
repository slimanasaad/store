<?php 
use App\Events\ShortageProductQuantity;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendPushNotification implements ShouldQueue
{
    public function handle(ShortageProductQuantity $event)
    {
        $product = $event->product;

        // إرسال الإشعار هنا حسب نوع التطبيق (مثلاً Firebase Cloud Messaging)
        // مثال:
        $deviceToken = $product->user->device_token;

        // فرضاً لديك خدمة إرسال إشعارات:
        NotificationService::sendToDevice($deviceToken, [
            'title' => 'تم تحديث حالة الطلب',
            'body' => "تم تغيير حالة طلبك إلى: {$product->status}",
        ]);
    }
}
