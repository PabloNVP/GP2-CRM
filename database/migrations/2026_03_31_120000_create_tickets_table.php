<?php

use App\Enums\PriorityTicketEnum;
use App\Enums\StateTicketEnum;
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
        Schema::create('tickets', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();
            $table->string('subject');
            $table->text('description');
            $table->enum('priority', PriorityTicketEnum::getDatabaseValues())
                ->default(PriorityTicketEnum::getDefaultValue())
                ->index();
            $table->enum('state', StateTicketEnum::getDatabaseValues())
                ->default(StateTicketEnum::getDefaultValue())
                ->index();
            $table->timestamps();
            $table->softDeletes();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
