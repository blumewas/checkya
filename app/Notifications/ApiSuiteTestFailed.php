<?php declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApiSuiteTestFailed extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $apiSuiteId,
        protected string $apiSuiteName,
        protected string $errorMessage,
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(__('notifications.failed.subject', [
                'name' => $this->apiSuiteName,
            ]))
            ->line(__('notifications.failed.intro', [
                'name' => $this->apiSuiteName,
                'error' => $this->errorMessage,
            ]))
            ->action(__('notifications.failed.action.view'), route(
                'filament.admin.resources.api-suites.view',
                [
                    'record' => $this->apiSuiteId,
                ],
            ))
            ->line(__('notifications.failed.outro'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
