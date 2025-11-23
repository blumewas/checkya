<?php declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('api_suites', function (Blueprint $table): void {
            $table->string('status')->after('name');
        });
    }

    public function down(): void {}
};
