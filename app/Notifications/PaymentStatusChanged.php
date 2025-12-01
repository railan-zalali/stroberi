<?php

namespace App\Notifications;

use App\Models\Transaksi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    protected $transaksi;
    protected $newStatus;

    /**
     * Create a new notification instance.
     *
     * @param Transaksi $transaksi
     * @param string $newStatus
     */
    public function __construct(Transaksi $transaksi, string $newStatus)
    {
        $this->transaksi = $transaksi;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
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
                    ->subject('Status Pembayaran Berubah')
                    ->greeting('Halo ' . $notifiable->name . ',')
                    ->line('Status pembayaran transaksi telah berubah.')
                    ->line('Transaksi: ' . $this->transaksi->keterangan)
                    ->line('Jumlah: Rp ' . number_format($this->transaksi->jumlah, 0, ',', '.'))
                    ->line('Status Baru: ' . $this->newStatus)
                    ->action('Lihat Detail Transaksi', url('/transaksi/' . $this->transaksi->id))
                    ->line('Terima kasih telah menggunakan sistem kami!');
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
            'transaksi_id' => $this->transaksi->id,
            'keterangan' => $this->transaksi->keterangan,
            'jumlah' => $this->transaksi->jumlah,
            'old_status' => $this->newStatus == 'Sudah Dibayar' ? 'Belum Dibayar' : 'Sudah Dibayar',
            'new_status' => $this->newStatus,
            'changed_at' => now(),
        ];
    }
}