<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table("vacunas", function (Blueprint $table) {
            $table->time("vaccination_time")->nullable()->after("vaccination_date");
        });

        Schema::table("vaccine_types", function (Blueprint $table) {
            $table->decimal("base_price", 10, 2)->default(0)->after("name");
        });
    }

    public function down(): void
    {
        Schema::table("vacunas", function (Blueprint $table) {
            $table->dropColumn("vaccination_time");
        });

        Schema::table("vaccine_types", function (Blueprint $table) {
            $table->dropColumn("base_price");
        });
    }
};