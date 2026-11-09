<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePacientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('cpf', 14)->unique();
            $table->string('rg', 20)->nullable();
            $table->date('data_nascimento');
            $table->enum('sexo', ['M', 'F', 'Outro'])->default('Outro');
            $table->string('telefone', 30);
            $table->string('email')->nullable();
            $table->string('tipo_sanguineo', 5)->nullable();
            
            // Dados clínicos prévios
            $table->text('alergias')->nullable();
            $table->text('medicamentos_uso_continuo')->nullable();
            $table->text('historico_cirurgico')->nullable();
            
            // Convênio / Pagamento
            $table->string('convenio', 50)->default('Particular');
            $table->string('numero_carteirinha', 50)->nullable();
            
            // Contato de emergência
            $table->string('contato_emergencia_nome', 100)->nullable();
            $table->string('contato_emergencia_telefone', 30)->nullable();
            
            // Endereço
            $table->string('cep', 10)->nullable();
            $table->string('endereco')->nullable();
            $table->string('numero', 20)->nullable();
            $table->string('bairro', 100)->nullable();
            $table->string('cidade', 100)->nullable();
            $table->string('estado', 2)->nullable();
            $table->text('observacoes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pacientes');
    }
}
