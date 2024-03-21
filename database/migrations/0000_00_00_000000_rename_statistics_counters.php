<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class RenameStatisticsCounters extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            // Crear nuevas columnas con los nombres deseados
            $table->integer('peak_connections_count')->after('peak_connection_count');
            $table->integer('websocket_messages_count')->after('websocket_message_count');
            $table->integer('api_messages_count')->after('api_message_count');
        });

        // Copiar datos de las columnas antiguas a las nuevas
        DB::statement('UPDATE websockets_statistics_entries SET peak_connections_count = peak_connection_count');
        DB::statement('UPDATE websockets_statistics_entries SET websocket_messages_count = websocket_message_count');
        DB::statement('UPDATE websockets_statistics_entries SET api_messages_count = api_message_count');

        // Eliminar columnas antiguas
        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            $table->dropColumn('peak_connection_count');
            $table->dropColumn('websocket_message_count');
            $table->dropColumn('api_message_count');
        });
    }
    

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            $table->renameColumn('peak_connections_count', 'peak_connection_count');
        });
        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            $table->renameColumn('websocket_messages_count', 'websocket_message_count');
        });
        Schema::table('websockets_statistics_entries', function (Blueprint $table) {
            $table->renameColumn('api_messages_count', 'api_message_count');
        });
    }
}
