<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ValidateClientsListingPagination extends Command
{
    protected $signature = 'clients:validate-listing {--keep-data : Conserva los datos de prueba en la base de datos}';

    protected $description = 'Valida escenarios 0, 1, 10 y 11+ registros para la paginación del listado de clientes';

    public function handle(): int
    {
        $table = (new Client())->getTable();

        if (! Schema::hasTable($table)) {
            $this->error("No existe la tabla [{$table}]. Ejecutá migraciones antes de validar.");

            return self::FAILURE;
        }

        $useTransaction = ! $this->option('keep-data');

        if ($useTransaction) {
            DB::beginTransaction();
        }

        try {
            $rows = [];

            foreach ([0, 1, 10, 11] as $total) {
                DB::table($table)->delete();

                $records = [];
                $timestamp = now();

                for ($index = 1; $index <= $total; $index++) {
                    $records[] = [
                        'firstname' => "Nombre {$total}-{$index}",
                        'lastname' => "Apellido {$total}-{$index}",
                        'email' => "cliente-{$total}-{$index}@example.com",
                        'phone' => "123456{$index}",
                        'address' => "Calle {$index}",
                        'company' => "Empresa {$index}",
                        'state' => 'activo',
                        'created_at' => $timestamp,
                        'updated_at' => $timestamp,
                    ];
                }

                if ($records !== []) {
                    DB::table($table)->insert($records);
                }

                $pageOne = Client::query()->orderByDesc('id')->paginate(10, ['*'], 'page', 1);
                $pageTwo = Client::query()->orderByDesc('id')->paginate(10, ['*'], 'page', 2);

                $isValid = $this->matchesExpected($total, $pageOne->count(), $pageOne->lastPage(), $pageTwo->count());

                $rows[] = [
                    'Escenario' => $total,
                    'Total' => $pageOne->total(),
                    'Pag1' => $pageOne->count(),
                    'UltPag' => $pageOne->lastPage(),
                    'Pag2' => $pageTwo->count(),
                    'OK' => $isValid ? 'SI' : 'NO',
                ];
            }

            $this->table(['Escenario', 'Total', 'Pag1', 'UltPag', 'Pag2', 'OK'], $rows);

            $hasErrors = collect($rows)->contains(fn (array $row): bool => $row['OK'] === 'NO');

            if ($hasErrors) {
                $this->error('La validación de paginación detectó inconsistencias.');

                if ($useTransaction && DB::transactionLevel() > 0) {
                    DB::rollBack();
                }

                return self::FAILURE;
            }

            if ($useTransaction && DB::transactionLevel() > 0) {
                DB::rollBack();
                $this->info('Validación exitosa. Los datos de prueba fueron revertidos.');
            } else {
                $this->warn('Validación exitosa. Los datos de prueba quedaron persistidos (--keep-data).');
            }

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            if ($useTransaction && DB::transactionLevel() > 0) {
                DB::rollBack();
            }

            $this->error('Error durante la validación: '.$exception->getMessage());

            return self::FAILURE;
        }
    }

    private function matchesExpected(int $total, int $pageOneCount, int $lastPage, int $pageTwoCount): bool
    {
        $expected = match ($total) {
            0 => [0, 1, 0],
            1 => [1, 1, 0],
            10 => [10, 1, 0],
            11 => [10, 2, 1],
            default => [0, 1, 0],
        };

        return $pageOneCount === $expected[0]
            && $lastPage === $expected[1]
            && $pageTwoCount === $expected[2];
    }
}
