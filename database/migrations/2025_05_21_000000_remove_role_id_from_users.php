<?php

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
        // First drop the foreign key constraint if it exists
        Schema::table('users', function (Blueprint $table) {
            // Check if the foreign key exists before trying to drop it
            $foreignKeys = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys('users');

            $foreignKeyExists = collect($foreignKeys)
                ->contains(function ($foreignKey) {
                    return $foreignKey->getLocalColumns() === ['role_id'];
                });

            if ($foreignKeyExists) {
                $table->dropForeign(['role_id']);
            }
        });

        // Then drop the column
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'role_id')) {
                $table->dropColumn('role_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->nullable();
            }
        });
    }
};
