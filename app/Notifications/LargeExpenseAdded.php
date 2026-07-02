<?php

namespace App\Notifications;

use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class LargeExpenseAdded extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Expense transactions at or above this amount trigger the notification.
     */
    const LARGE_EXPENSE_THRESHOLD = 5000;

    public function __construct(protected Transaction $transaction)
    {
    }

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'transaction_id' => $this->transaction->id,
            'account_id'     => $this->transaction->account_id,
            'account_name'   => $this->transaction->account?->name,
            'amount'         => $this->transaction->amount,
            'note'           => $this->transaction->note,
            'message'        => sprintf(
                'A large expense of %s was recorded on %s.',
                number_format((float) $this->transaction->amount, 2),
                $this->transaction->account?->name ?? 'your account',
            ),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->id,
            'type'            => static::class,
            'notifiable_type' => get_class($this->transaction->user),
            'notifiable_id'   => $this->transaction->user_id,
            'data'            => $this->toDatabase($this->transaction->user),
            'read_at'         => null,
            'created_at'      => now()->toJSON(),
        ];
    }
}
