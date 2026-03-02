<?php

namespace App\Livewire\Clients;

use App\Models\Client;
use Livewire\Component;

class AddClient extends Component
{
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $phone = '';
    public string $address = '';
    public string $company = '';

    public function render()
    {
        return view('livewire.clients.add-client');
    }

    public function saveClient()
    {
        $this->validate();

        $status = Client::query()->create([
            'firstname' => $this->firstname,
            'lastname' => $this->lastname,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'company' => $this->company,
        ]);

        if ($status) {
            session()->flash('message', 'Cliente agregado exitosamente.');
        } else {
            session()->flash('error', 'Hubo un error al agregar el cliente.');
        }

        return redirect()->route('clients.index');
    }

    public function rules(): array
    {
        return [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients,email'],
            'phone' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'company' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages() : array
    {
        return [
            'firstname.required' => 'El campo nombre es obligatorio.',
            'firstname.string' => 'El campo nombre debe ser una cadena de texto.',
            'firstname.max' => 'El campo nombre no puede tener más de 255 caracteres.',
            
            'lastname.required' => 'El campo apellido es obligatorio.',
            'lastname.string' => 'El campo apellido debe ser una cadena de texto.',
            'lastname.max' => 'El campo apellido no puede tener más de 255 caracteres.',
            
            'email.required' => 'El campo correo es obligatorio.',
            'email.string' => 'El campo correo debe ser una cadena de texto.',
            'email.email' => 'El correo debe ser una dirección de correo válida.',
            'email.max' => 'El campo correo no puede tener más de 255 caracteres.',
            'email.unique' => 'El correo electrónico ya está registrado.',
            
            'phone.string' => 'El campo teléfono debe ser una cadena de texto.',
            'phone.max' => 'El campo teléfono no puede tener más de 255 caracteres.',
            
            'address.string' => 'El campo dirección debe ser una cadena de texto.',
            
            'company.string' => 'El campo empresa debe ser una cadena de texto.',
            'company.max' => 'El campo empresa no puede tener más de 255 caracteres.',
        ];
    }
}
