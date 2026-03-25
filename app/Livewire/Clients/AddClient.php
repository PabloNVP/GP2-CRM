<?php

namespace App\Livewire\Clients;

use App\Livewire\Actions\Clients\InsertClient;
use App\Livewire\Actions\Clients\UpdateClient;
use App\Models\Client;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Livewire\Component;

class AddClient extends Component
{
    public ?Client $client = null;

    public ?int $clientId = null;
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $company = '';

    public function mount(?Client $client = null): void
    {
        if (! $client || ! $client->exists) {
            return;
        }

        $this->client = $client;
        $this->clientId = $client->id;
        $this->firstname = $client->firstname;
        $this->lastname = $client->lastname;
        $this->email = $client->email;
        $this->phone = $client->phone ?? '';
        $this->address = $client->address ?? '';
        $this->company = $client->company ?? '';
    }

    public function render()
    {
        return view('clients.add-client');
    }

    public function saveClient(InsertClient $insertClient, UpdateClient $updateClient)
    {
        $payload = $this->validate();
        $isEditing = $this->isEditing();
        $status = false;

        try {
            if ($isEditing && $this->clientId !== null) {
                $status = $updateClient($payload, $this->clientId);
            } else {
                $status = $insertClient($payload);
            }
        } catch (ModelNotFoundException) {
            session()->flash('error', 'El cliente seleccionado no existe.');

            return redirect()->route('clients.index');
        }

        if ($status) {
            session()->flash(
                'message',
                $isEditing ? 'Cliente actualizado exitosamente.' : 'Cliente agregado exitosamente.'
            );
        } else {
            session()->flash(
                'error',
                $isEditing ? 'Hubo un error al actualizar el cliente.' : 'Hubo un error al agregar el cliente.'
            );
        }

        return redirect()->route('clients.index');
    }

    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'max:255'],
            'lastname' => ['required', 'string', 'regex:/^[\p{L}\s]+$/u', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('clients', 'email')->ignore($this->clientId),
            ],
            'phone' => ['nullable', 'string', 'regex:/^[\+\d\s\-\(\)]+$/', 'max:255'],
            'address' => ['nullable', 'ascii', 'string'],
            'company' => ['nullable', 'ascii', 'string', 'regex:/^[\p{L}\d\s]+$/u', 'max:255'],
        ];
    }

    public function isEditing(): bool
    {
        return $this->clientId !== null;
    }

    public function messages() : array
    {
        return [
            'firstname.required' => 'El campo nombre es obligatorio.',
            'firstname.string' => 'El campo nombre debe ser una cadena de texto.',
            'firstname.regex' => 'El campo nombre solo puede contener letras y espacios.',
            'firstname.max' => 'El campo nombre no puede tener más de 255 caracteres.',

            'lastname.required' => 'El campo apellido es obligatorio.',
            'lastname.string' => 'El campo apellido debe ser una cadena de texto.',
            'lastname.regex' => 'El campo apellido solo puede contener letras y espacios.',
            'lastname.max' => 'El campo apellido no puede tener más de 255 caracteres.',

            'email.required' => 'El campo correo es obligatorio.',
            'email.string' => 'El campo correo debe ser una cadena de texto.',
            'email.email' => 'El correo debe ser una dirección de correo válida.',
            'email.max' => 'El campo correo no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',

            'phone.string' => 'El campo teléfono debe ser una cadena de texto.',
            'phone.max' => 'El campo teléfono no puede tener más de 255 caracteres.',
            'phone.regex' => 'El teléfono solo puede contener números, espacios, guiones, paréntesis y el signo +.',

            'address.string' => 'El campo dirección debe ser una cadena de texto.',

            'company.string' => 'El campo empresa debe ser una cadena de texto.',
            'company.regex' => 'El campo empresa solo puede contener letras, números y espacios.',
            'company.max' => 'El campo empresa no puede tener más de 255 caracteres.',
        ];
    }
}
