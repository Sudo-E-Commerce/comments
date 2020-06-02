<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('comments', function (Blueprint $table) {
            // ID
            $table->bigIncrements('id');
            // ID quản trị viên
            $table->integer('admin_id')->default(0);
            // ID comment cha
            $table->integer('parent_id')->default(0);
            // Thông tin người bình luận
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('ip')->nullable();
            // Thông tin bài viết
            $table->string('type');
            $table->integer('type_id');
            // Nội dung
            $table->longtext('content');
            // Ảnh
            $table->text('image')->nullable();
            // (0 Ẩn | 1 Đã đăng | -1 xóa)
            $table->tinyInteger('status')->default(1);
            // Thời gian bình luận
            $table->timestamp('time')->nullable();
            // Đánh index
            $table->index(['type', 'type_id','status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
}
