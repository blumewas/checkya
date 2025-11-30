<?php declare(strict_types=1);

namespace App\Notifications;

use App\Data\TestResult;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApiSuiteTestReport extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        protected string $apiSuiteId,
        protected string $apiSuiteName,
        protected TestResult $results,
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
            ->subject(__('notifications.report.subject', [
                'name' => $this->apiSuiteName,
            ]))
            ->markdown('notifications.test-report', [
                'name' => $this->apiSuiteName,
                'id' => $this->apiSuiteId,
                'expectations' => $this->results->expectationResult(),
            ]);
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
