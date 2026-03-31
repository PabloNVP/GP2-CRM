<?php

namespace App\Livewire\Actions\Tickets;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Builder;

final readonly class ListingTicket
{
    public function __invoke(
        string $search = '',
        string $priorityFilter = '',
        string $stateFilter = '',
        string $productFilter = '',
        string $fromDate = '',
        string $toDate = '',
    ): iterable
    {
        $query = Ticket::query()
            ->with(['client', 'product'])
            ->orderByDesc('id');

        $search = trim($search);

        if ($search !== '') {
            $query->where(function (Builder $subQuery) use ($search): void {
                if (ctype_digit($search)) {
                    $subQuery->orWhere('id', (int) $search);
                }

                $subQuery->orWhere('subject', 'like', "%{$search}%")
                    ->orWhereHas('client', function (Builder $clientQuery) use ($search): void {
                        $clientQuery
                            ->where('firstname', 'like', "%{$search}%")
                            ->orWhere('lastname', 'like', "%{$search}%")
                            ->orWhereRaw("firstname || ' ' || lastname like ?", ["%{$search}%"]);
                    });
            });
        }

        if ($priorityFilter !== '') {
            $query->where('priority', $priorityFilter);
        }

        if ($stateFilter !== '') {
            $query->where('state', $stateFilter);
        }

        if ($productFilter !== '') {
            $query->where('product_id', (int) $productFilter);
        }

        if ($fromDate !== '') {
            $query->whereDate('created_at', '>=', $fromDate);
        }

        if ($toDate !== '') {
            $query->whereDate('created_at', '<=', $toDate);
        }

        return $query->paginate(10);
    }
}
