<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Posts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function(Blueprint $table)
        {
            $table->increments('id');
            $table -> integer('author_id') ->unsigned()->default(0);
            $table->foreign('author_id')
                ->references('id')->on('users')
                ->onDelete('cascade');
            $table->string('title');
            $table->text('body');
            $table->string('slug');
            $table->boolean('active');
            $table->timestamps();
            $table->unique([DB::raw('title(100)')]);
            $table->unique([DB::raw('slug(100)')]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function($table) {
            $table->dropIndex([DB::raw('title(100)')]);
            $table->dropIndex([DB::raw('slug(100')]);
        });
    }
}
