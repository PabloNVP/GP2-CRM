<?php

namespace App\Livewire\Actions\Invoices;

use App\Models\Invoice;
use Illuminate\Database\Eloquent\Builder;

final readonly class ListingInvoice
{
    public function __invoke(
        string $search = '',
        string $stateFilter = '',
        string $fromDate = '',
        string $toDate = '',
    ): iterable {
        $query = Invoice::query()
            ->with(['order.client'])
            ->orderByDesc('id');

        $search = trim($search);

        if ($search !== '') {
            $query->where(function (Builder $subQuery) use ($search): void {
                $subQuery->where('number', 'like', "%{$search}%")
                    ->orWhereHas('order.client', function (Builder $clientQuery) use ($search): void {
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
            $query->whereDate('issue_date', '>=', $fromDate);
        }

        if ($toDate !== '') {
            $query->whereDate('issue_date', '<=', $toDate);
        }

        return $query->paginate(10);
    }
}
