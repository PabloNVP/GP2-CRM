<?php

namespace App\Livewire\Actions\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

final readonly class ListingUsers
{
    public function __invoke(
        string $search = '',
        string $roleFilter = '',
        string $stateFilter = '',
    ): iterable
    {
        $query = User::query()->orderByDesc('id');

        $search = trim($search);

        if ($search !== '') {
            $query->where(function (Builder $subQuery) use ($search): void {
                $subQuery->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($roleFilter !== '') {
            $query->where('role', $roleFilter);
        }

        if ($stateFilter !== '') {
            $query->where('state', $stateFilter);
        }

        return $query->paginate(10);
    }
}
