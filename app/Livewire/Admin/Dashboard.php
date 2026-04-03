<?php

namespace App\Livewire\Admin;

use App\Enums\RoleEnum;
use App\Enums\StateEnum;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

final class Dashboard extends Component
{
    public function render(): View
    {
        $totalUsers = User::query()->count();

        $activeUsers = User::query()
            ->where('state', StateEnum::ACTIVE->value)
            ->count();

        $inactiveUsers = User::query()
            ->where('state', StateEnum::INACTIVE->value)
            ->count();

        $countsByRole = User::query()
            ->select('role', DB::raw('COUNT(*) as total'))
            ->groupBy('role')
            ->pluck('total', 'role');

        $rolesDistribution = collect(RoleEnum::cases())
            ->map(fn (RoleEnum $role): array => [
                'role' => $role,
                'count' => (int) ($countsByRole[$role->value] ?? 0),
            ]);

        return view('admin.dashboard', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'rolesDistribution' => $rolesDistribution,
        ]);
    }
}
