<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('review_assignment', function (Blueprint $table) {
            // Section A: ratings (1–5)
            // Section A: ratings (1–5 scale)
            $table->unsignedTinyInteger('rate_originality')->nullable()->after('score');
            $table->unsignedTinyInteger('rate_literature_depth')->nullable()->after('rate_originality');
            $table->unsignedTinyInteger('rate_methodology')->nullable()->after('rate_literature_depth');
            $table->unsignedTinyInteger('rate_novelty')->nullable()->after('rate_methodology');
            $table->unsignedTinyInteger('rate_clarity')->nullable()->after('rate_novelty');
            $table->unsignedTinyInteger('rate_contribution')->nullable()->after('rate_clarity');

            // Section B: accept for presentation? (Yes/No)
            $table->string('accept_presentation', 10)->nullable()->after('rate_contribution'); // Yes/No

            // Section C: comments
            $table->text('overall_comments')->nullable()->after('comment'); // not revealed to author
            $table->text('comments_to_author')->nullable()->after('overall_comments'); // revealed to author

            // Section D/E: indexing recommendation
            $table->string('propose_scopus', 10)->nullable()->after('comments_to_author');     // Yes/No
            $table->string('propose_non_scopus', 10)->nullable()->after('propose_scopus');   // Yes/No

            // File upload (edited/revised)
            $table->string('revised_file_path')->nullable()->after('propose_non_scopus');

            // timestamps for submission
            $table->dateTime('submitted_at')->nullable()->after('revised_file_path');
        });
    }

    public function down(): void
    {
        Schema::table('review_assignment', function (Blueprint $table) {
            $table->dropColumn([
                'rate_originality',
                'rate_literature_depth',
                'rate_methodology',
                'rate_novelty',
                'rate_clarity',
                'rate_contribution',
                'accept_presentation',
                'overall_comments',
                'comments_to_author',
                'propose_scopus',
                'propose_non_scopus',
                'revised_file_path',
                'submitted_at',
            ]);
        });
    }
};
