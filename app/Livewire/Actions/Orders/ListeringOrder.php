<?php

namespace App\Livewire\Actions\Orders;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;

final readonly class ListeringOrder
{
    public function __invoke(
        string $search = '',
        string $stateFilter = '',
        string $fromDate = '',
        string $toDate = '',
    ): iterable
    {
        $query = Order::query()
            ->with('client')
            ->orderByDesc('id');

        $search = trim($search);

        if ($search !== '') {
            $query->where(function (Builder $subQuery) use ($search): void {
                if (ctype_digit($search)) {
                    $subQuery->orWhere('id', (int) $search);
                }

                $subQuery->orWhereHas('client', function (Builder $clientQuery) use ($search): void {
                    $clientQuery
                        ->where('firstname', 'like', "%{$search}%")
                        ->orWhere('lastname', 'like', "%{$search}%")
                        ->orWhereRaw("firstname || ' ' || lastname like ?", ["%{$search}%"]);
                });
            });
        }

        if ($stateFilter !== '') {
            $query->where('state', $stateFilter);
        }

        if ($fromDate !== '') {
            $query->whereDate('date', '>=', $fromDate);
        }

        if ($toDate !== '') {
            $query->whereDate('date', '<=', $toDate);
        }

        return $query->paginate(10);
    }
}
