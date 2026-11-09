<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            // Direção Clínica
            [
                'name' => 'Dr. Andrik Cardoso',
                'email' => 'admin@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'crm' => 'CRM/PB 8412',
                'especialidade' => 'Clínica Geral & Gestão Médica',
                'telefone' => '(83) 98877-1001',
                'ativo' => true,
            ],

            // Corpo Médico
            [
                'name' => 'Dr. Juliane',
                'email' => 'dr.juliane@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'medico',
                'crm' => 'CRM/PB 9154',
                'especialidade' => 'Pediatria & Clínica Geral',
                'telefone' => '(83) 98877-1002',
                'ativo' => true,
            ],
            [
                'name' => 'Dr. Pedro Miguel',
                'email' => 'pedro.miguel@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'medico',
                'crm' => 'CRM/PB 7820',
                'especialidade' => 'Cardiologia',
                'telefone' => '(83) 98877-1003',
                'ativo' => true,
            ],
            [
                'name' => 'Dr. Mauricio Cunha',
                'email' => 'mauricio.cunha@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'medico',
                'crm' => 'CRM/PB 6531',
                'especialidade' => 'Dermatologia',
                'telefone' => '(83) 98877-1004',
                'ativo' => true,
            ],
            [
                'name' => 'Dr. Vinicius Oliveira',
                'email' => 'vinicius.oliveira@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'medico',
                'crm' => 'CRM/PB 11204',
                'especialidade' => 'Ortopedia e Traumatologia',
                'telefone' => '(83) 98877-1005',
                'ativo' => true,
            ],
            [
                'name' => 'Dr. Matheus Lins',
                'email' => 'matheus.lins@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'medico',
                'crm' => 'CRM/PB 12480',
                'especialidade' => 'Oftalmologia',
                'telefone' => '(83) 98877-1006',
                'ativo' => true,
            ],
            [
                'name' => 'Dr. Maycon',
                'email' => 'maycon@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'medico',
                'crm' => 'CRM/PB 13952',
                'especialidade' => 'Neurologia',
                'telefone' => '(83) 98877-1007',
                'ativo' => true,
            ],

            // Recepção
            [
                'name' => 'Matheus Brito',
                'email' => 'matheus.brito@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'recepcao',
                'crm' => null,
                'especialidade' => 'Recepção & Atendimento ao Paciente',
                'telefone' => '(83) 98877-1008',
                'ativo' => true,
            ],
            [
                'name' => 'Ozair',
                'email' => 'ozair@clinicaandrik.com.br',
                'password' => Hash::make('password'),
                'role' => 'recepcao',
                'crm' => null,
                'especialidade' => 'Recepção & Triagem',
                'telefone' => '(83) 98877-1009',
                'ativo' => true,
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
    }
}
