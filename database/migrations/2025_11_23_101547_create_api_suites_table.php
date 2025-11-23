<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('api_suites', function (Blueprint $table): void {
            $table->ulid('id');

            $table->string('name');
            $table->string('cron_schedule');
            $table->text('config');
            $table->text('secrets')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_suites');
    }
};
