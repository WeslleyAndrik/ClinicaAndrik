<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProntuariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prontuarios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id');
            $table->unsignedBigInteger('medico_id');
            $table->unsignedBigInteger('consulta_id')->nullable();
            $table->dateTime('data_registro');
            
            // Anamnese
            $table->text('queixa_principal');
            $table->text('historia_doenca_atual')->nullable();
            
            // Sinais Vitais & Exame Físico
            $table->string('pressao_arterial', 20)->nullable();
            $table->integer('frequencia_cardiaca')->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();
            $table->decimal('peso', 5, 2)->nullable();
            $table->decimal('altura', 4, 2)->nullable();
            $table->text('exame_fisico')->nullable();
            
            // Diagnóstico & CID-10
            $table->text('diagnostico');
            $table->string('cid10', 100)->nullable();
            
            // Conduta e Tratamento
            $table->text('prescricao_medica')->nullable();
            $table->text('exames_solicitados')->nullable();
            $table->text('conduta_plano')->nullable();

            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('medico_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('consulta_id')->references('id')->on('consultas')->onDelete('set null');

            $table->index(['paciente_id', 'data_registro']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prontuarios');
    }
}
