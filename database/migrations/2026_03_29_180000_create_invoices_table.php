<?php

use App\Enums\StateInvoiceEnum;
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
        Schema::create('invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')
                ->constrained('orders')
                ->cascadeOnDelete()
                ->unique();
            $table->string('number', 50)->unique();
            $table->date('issue_date')->index();
            $table->decimal('total_amount', 10, 2);
            $table->enum('state', StateInvoiceEnum::getDatabaseValues())
                ->default(StateInvoiceEnum::getDefaultValue())
                ->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
