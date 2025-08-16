<?php

namespace App\Notifications;

use App\ReplacementRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReplacementRequestStatusChangedNotifi extends Notification implements ShouldQueue
{
    use Queueable;

    protected $replacement_request;
    
    protected $notifi_data;
    
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ReplacementRequest $replacement_request, $notifi_data)
    {
        $this->replacement_request = $replacement_request;
        $this->notifi_data = $notifi_data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $request = $this->replacement_request;

        return (new MailMessage)
                    ->subject('Изменение статуса запроса о замене занятия')           
                    ->greeting('Здравствуйте, уважаемый '.$this->notifi_data['teacher_name'].'!')            
                    ->line('Сообщаем, что статус замены, в которой Вы участвуете:')
                    ->line($request->name)
                    ->line('изменился с "'.$this->notifi_data['old_status'].'" на "'.$this->notifi_data['new_status'].'".')
                    ->action('В личный кабинет', route('my_replacement_requests'))
                    ->salutation('Менеджер расписания');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Определить, какие очереди следует использовать для каждого канала уведомления.
     *
     * @return array
     */
    public function viaQueues()
    {
        return [
            'mail' => 'email',
        ];
    }
}
