<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void {
    Schema::create('follows', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_id')->constrained();  // Follower
      $table->unsignedBigInteger('followeduser'); // Column being referenced
      $table->foreign('followeduser')->references('id')->on('users'); // States followeduser references the id coulmn of the user table
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void {
    Schema::dropIfExists('follows');
  }
};
