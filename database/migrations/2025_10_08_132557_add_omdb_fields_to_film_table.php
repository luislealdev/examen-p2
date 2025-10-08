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
        Schema::table('film', function (Blueprint $table) {
            // Campos adicionales de OMDB
            $table->string('imdb_id', 20)->nullable()->unique()->after('title');
            $table->string('poster_url')->nullable()->after('description');
            $table->decimal('imdb_rating', 3, 1)->nullable()->after('rating');
            $table->integer('imdb_votes')->nullable()->after('imdb_rating');
            $table->integer('metascore')->nullable()->after('imdb_votes');
            $table->text('plot')->nullable()->after('description'); // Plot más detallado que description
            $table->string('awards')->nullable()->after('special_features');
            $table->string('box_office', 50)->nullable()->after('awards');
            $table->string('production', 200)->nullable()->after('box_office');
            $table->string('website')->nullable()->after('production');
            $table->json('writers')->nullable()->after('website'); // Array de escritores
            $table->json('countries')->nullable()->after('writers'); // Array de países
            $table->boolean('imported_from_omdb')->default(false)->after('countries');
            
            // Índices
            $table->index('imdb_id');
            $table->index('imdb_rating');
            $table->index('imported_from_omdb');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('film', function (Blueprint $table) {
            $table->dropIndex(['imdb_id']);
            $table->dropIndex(['imdb_rating']);
            $table->dropIndex(['imported_from_omdb']);
            
            $table->dropColumn([
                'imdb_id',
                'poster_url',
                'imdb_rating',
                'imdb_votes',
                'metascore',
                'plot',
                'awards',
                'box_office',
                'production',
                'website',
                'writers',
                'countries',
                'imported_from_omdb'
            ]);
        });
    }
};
