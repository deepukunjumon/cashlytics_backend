<?php

namespace App\Notifications;

use App\Models\Budget;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class BudgetExceeded extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Budget $budget, protected float $spent)
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
            'budget_id'     => $this->budget->id,
            'category_id'   => $this->budget->category_id,
            'category_name' => $this->budget->category?->name,
            'budget_amount' => $this->budget->amount,
            'spent'         => $this->spent,
            'message'       => sprintf(
                'Your budget of %s for %s has been exceeded — %s spent so far.',
                number_format((float) $this->budget->amount, 2),
                $this->budget->category?->name ?? 'this category',
                number_format($this->spent, 2),
            ),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }

    /**
     * Mirrors LargeExpenseAdded::broadcastWith() — see that class for why this
     * override is needed instead of relying on Laravel's default flattening.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id'              => $this->id,
            'type'            => static::class,
            'notifiable_type' => get_class($this->budget->user),
            'notifiable_id'   => $this->budget->user_id,
            'data'            => $this->toDatabase($this->budget->user),
            'read_at'         => null,
            'created_at'      => now()->toJSON(),
        ];
    }
}
