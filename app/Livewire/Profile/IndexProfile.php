<?php

namespace App\Livewire\Profile;

use Livewire\Component;

class IndexProfile extends Component
{
    public string $tab = 'info';

    /**
     * @var array<int, string>
     */
    private const ALLOWED_TABS = ['info', 'password', 'delete'];

    public function setTab(string $tab): void
    {
        if (! in_array($tab, self::ALLOWED_TABS, true)) {
            return;
        }

        $this->tab = $tab;
    }

    public function render()
    {
        return view('profile.index-profile');
    }
}
