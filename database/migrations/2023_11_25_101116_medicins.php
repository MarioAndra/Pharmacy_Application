<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
return new class extends Migration
{

    public function up(): void
    {
        Schema::create('medicins', function (Blueprint $table) {
            $table->id();
            $table->string('Scientific_name');
            $table->string('commercial_name');
            $table->string('category');
            $table->integer('quantity');
            $table->string('Manufacture_Company');
            $table->double('price');
            $table->string('Expiry_data');
            $table->integer('warehouse_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicins');
    }
};
