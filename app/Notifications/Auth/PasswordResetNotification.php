<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * パスワードリセット通知
 * Class PasswordResetNotification
 * @package App\Notifications\Auth
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PasswordResetNotification extends Notification
{
    use Queueable;
    /**
     * @var string トークン
     */
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('【' . config('app.name') . '】パスワード再設定')
            ->line('パスワードを再設定するには、次の「パスワード再設定」ボタンを押下してください。')
            ->action('パスワード再設定', url(config('app.url') . route('password.reset', $this->token, false)))
            ->line('このメールに心当たりがない場合、何も行わずにこのメールを破棄してください。');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}