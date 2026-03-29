<?php

use App\Enums\StateOrderEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->date('date')->index();
            $table->enum('state', StateOrderEnum::getDatabaseValues())
                ->default(StateOrderEnum::getDefaultValue())
                ->index();
            $table->decimal('total', 10, 2)->default(0);
            $table->text('observations')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
