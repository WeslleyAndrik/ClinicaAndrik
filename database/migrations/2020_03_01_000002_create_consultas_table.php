<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConsultasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id');
            $table->unsignedBigInteger('medico_id');
            $table->dateTime('data_hora');
            $table->integer('duracao_minutos')->default(30);
            $table->string('especialidade', 100);
            $table->enum('tipo_atendimento', ['primeira_consulta', 'retorno', 'rotina', 'urgencia'])->default('rotina');
            $table->enum('status', ['agendada', 'confirmada', 'aguardando', 'em_atendimento', 'finalizada', 'cancelada'])->default('agendada');
            $table->text('motivo')->nullable();
            $table->string('convenio', 50)->default('Particular');
            $table->decimal('valor', 10, 2)->default(0.00);
            $table->text('observacoes')->nullable();
            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('medico_id')->references('id')->on('users')->onDelete('cascade');
            $table->index(['data_hora', 'medico_id']);
            $table->index(['paciente_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('consultas');
    }
}
