<?php

namespace App\Livewire\Actions;

use Illuminate\Database\Eloquent\Model;

abstract readonly class BaseUpdateAction
{
    /**
     * @param  array<string, mixed>  $payload
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function __invoke(array $payload, int $id): bool
    {
        $modelClass = $this->modelClass();
        $model = $modelClass::query()->findOrFail($id);

        $model->fill($payload);

        return $model->isDirty() ? $model->save() : true;
    }

    /**
     * @return class-string<Model>
     */
    abstract protected function modelClass(): string;
}