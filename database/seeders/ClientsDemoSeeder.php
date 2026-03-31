<?php

namespace Database\Seeders;

use App\Enums\StateEnum;
use App\Models\Client;
use Illuminate\Database\Seeder;

class ClientsDemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $clients = [
            ['firstname' => 'Nicolas', 'lastname' => 'Martinez', 'email' => 'nicolas.martinez@orion.com', 'company' => 'Orion Tech'],
            ['firstname' => 'Camila', 'lastname' => 'Suarez', 'email' => 'camila.suarez@vertex.com', 'company' => 'Vertex Solutions'],
            ['firstname' => 'Martin', 'lastname' => 'Lopez', 'email' => 'martin.lopez@nimbus.com', 'company' => 'Nimbus Group'],
            ['firstname' => 'Lucia', 'lastname' => 'Fernandez', 'email' => 'lucia.fernandez@andesdata.com', 'company' => 'Andes Data'],
            ['firstname' => 'Joaquin', 'lastname' => 'Gomez', 'email' => 'joaquin.gomez@deltaapps.com', 'company' => 'Delta Apps'],
            ['firstname' => 'Valentina', 'lastname' => 'Diaz', 'email' => 'valentina.diaz@summit.io', 'company' => 'Summit IO'],
            ['firstname' => 'Franco', 'lastname' => 'Ruiz', 'email' => 'franco.ruiz@borealsoft.com', 'company' => 'Boreal Soft'],
            ['firstname' => 'Agustina', 'lastname' => 'Pereyra', 'email' => 'agustina.pereyra@lumencloud.com', 'company' => 'Lumen Cloud'],
            ['firstname' => 'Santiago', 'lastname' => 'Alvarez', 'email' => 'santiago.alvarez@primenet.com', 'company' => 'Prime Net'],
            ['firstname' => 'Micaela', 'lastname' => 'Rossi', 'email' => 'micaela.rossi@atlaslabs.com', 'company' => 'Atlas Labs'],
            ['firstname' => 'Tomas', 'lastname' => 'Vega', 'email' => 'tomas.vega@infinityhq.com', 'company' => 'Infinity HQ'],
            ['firstname' => 'Julieta', 'lastname' => 'Morales', 'email' => 'julieta.morales@horizon.dev', 'company' => 'Horizon Dev'],
            ['firstname' => 'Ignacio', 'lastname' => 'Castro', 'email' => 'ignacio.castro@novaone.com', 'company' => 'Nova One'],
            ['firstname' => 'Florencia', 'lastname' => 'Mendez', 'email' => 'florencia.mendez@quantumline.com', 'company' => 'Quantum Line'],
            ['firstname' => 'Bruno', 'lastname' => 'Silva', 'email' => 'bruno.silva@uplinksystems.com', 'company' => 'Uplink Systems'],
        ];

        foreach ($clients as $index => $client) {
            Client::query()->updateOrCreate(
                ['email' => $client['email']],
                [
                    'firstname' => $client['firstname'],
                    'lastname' => $client['lastname'],
                    'phone' => '11'.str_pad((string) (10000000 + $index), 8, '0', STR_PAD_LEFT),
                    'address' => 'Calle Demo '.($index + 1),
                    'company' => $client['company'],
                    'state' => StateEnum::ACTIVE,
                ],
            );
        }
    }
}
