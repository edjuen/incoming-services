<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Service;
use Filament\Notifications\Notification as FilamentNotification;

class NewServiceReceivedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Service $service
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return FilamentNotification::make()
            ->title('Nuevo servicio recibido')
            ->body('Folio: ' . $this->service->folio . ' / ' . ($this->service->insured_name ?? 'Sin asegurado'))
            ->success()
            ->getDatabaseMessage();
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
