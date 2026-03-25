<?php

namespace App\Livewire\Actions;

use Illuminate\Database\Eloquent\Model;

abstract readonly class BaseInsertAction
{
    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __invoke(array $payload): bool
    {
        $modelClass = $this->modelClass();
        $model = new $modelClass($payload);

        return $model->save();
    }
}