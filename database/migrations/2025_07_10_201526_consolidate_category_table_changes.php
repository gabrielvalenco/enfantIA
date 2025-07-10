<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Este arquivo consolida as alterações das seguintes migrations:
     * - 2025_02_06_142247_add_color_to_categories_table.php
     * - 2025_02_06_143446_modify_description_in_categories_table.php
     * 
     * Essas alterações são aplicadas à definição original da tabela categories
     */
    public function up(): void
    {
        // Não faz nada se a migration for executada em um banco existente
        // As alterações já foram aplicadas pelas migrations originais
        if (Schema::hasColumn('categories', 'color')) {
            return;
        }
        
        // Em um novo banco, essas alterações serão aplicadas diretamente
        Schema::table('categories', function (Blueprint $table) {
            // Adiciona a coluna color (da migration 2025_02_06_142247_add_color_to_categories_table)
            $table->string('color')->default('#2C3E50')->after('description');
            
            // Modifica a coluna description para ser nullable (da migration 2025_02_06_143446_modify_description_in_categories_table)
            $table->text('description')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            // Remove a coluna color
            if (Schema::hasColumn('categories', 'color')) {
                $table->dropColumn('color');
            }
            
            // Reverte a coluna description para não nullable
            if (Schema::hasColumn('categories', 'description')) {
                $table->text('description')->nullable(false)->change();
            }
        });
    }
};
