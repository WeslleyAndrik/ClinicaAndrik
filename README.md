# Clínica Andrik - Sistema de Gestão Médica

Sistema web para gestão de clínicas médicas desenvolvido em **PHP / Laravel 7**, contemplando controle de pacientes, agendamento de consultas com verificação de conflito de horário, prontuário médico eletrônico (padrão CFM), linha do tempo clínica e relatórios gerenciais.

---

## Módulos e Funcionalidades

### 1. Controle de Acesso e Perfis
- **`admin` (Direção Clínica)**: Acesso integral ao sistema, relatórios, agendamentos, prontuários e gestão de equipe/usuários.
- **`medico` (Corpo Clínico)**: Acesso à fila de espera, agenda médica, registro e edição de prontuários próprios e histórico clínico de pacientes.
- **`recepcao` (Atendimento)**: Cadastro de pacientes, marcação de consultas e controle do fluxo de recepção.

### 2. Cadastro de Pacientes e Validações de Campos
- **CPF**: Validação algorítmica completa dos dígitos verificadores (Módulo 11), bloqueio de sequências repetidas, máscara automática (`000.000.000-00`) e restrição de unicidade no banco de dados.
- **Endereço, Número e Validação nos Correios (CEP, UF e Cidade)**:
  - **Consulta e Validação nos Correios**: Ao informar os 8 dígitos do **CEP**, o sistema consulta a base dos Correios (`CorreiosCepService`), preenche automaticamente logradouro, bairro, cidade e estado (UF), rejeita CEPs inexistentes e impede divergências entre o CEP informado e os campos de **Cidade** e **Estado (UF)**.
  - **Número do Endereço**: Restrito exclusivamente a algarismos numéricos inteiros positivos maiores que zero.
- **Telefone e Contato de Emergência**: Validação de DDD + número fixo ou móvel (`(00) 00000-0000`).
- **Dados Civis e Clínicos**: Validação de nome completo (apenas letras), data de nascimento retroativa válida, cálculo automático de idade, tipo sanguíneo e destaque para **Alergias Conhecidas**.

### 3. Agendamento de Consultas e Notificação via WhatsApp (Evolution API)
- Agenda médica com filtros por data, profissional e status.
- **Disparo Automático via Evolution API (WhatsApp)**: Ao agendar uma nova consulta (ou clicar em *Disparar Confirmação via WhatsApp* nos detalhes da consulta), o sistema formata o número do paciente com DDI `55` + DDD + número e realiza o disparo HTTP `POST /message/sendText/{instance}` para a **Evolution API** com os dados de data, horário, médico e endereço da clínica.
- **Prevenção de Conflito de Horários**: Impede agendamentos sobrepostos para o mesmo médico no mesmo intervalo.
- Fluxo de atendimento: `Agendada` -> `Confirmada` -> `Aguardando na Recepção` -> `Em Atendimento` -> `Finalizada` (ou `Cancelada`).

### 4. Prontuário Médico Eletrônico e Histórico
- Registro de Anamnese (Queixa Principal e HDA), Exame Físico, Hipótese Diagnóstica, Código CID-10, Prescrição Farmacológica, Solicitação de Exames e Conduta.
- **Validação de Sinais Vitais**: Pressão Arterial no padrão `sistólica/diastólica` (ex: `120/80`), Frequência Cardíaca (`30-250 bpm`), Temperatura (`30-45 °C`), Peso (`0.5-400 kg`), Altura (`0.30-2.50 m`) e cálculo automático de **IMC** com classificação clínica.
- **Linha do Tempo Clínica**: Histórico cronológico unificado de todas as consultas e evoluções médicas do paciente.
- **Relatórios de Atendimento**: Filtros combinados por período, médico responsável, paciente, convênio e status, com totalizadores financeiros e layout de impressão.

---

## Como Executar o Projeto

### Opção 1: Execução Local Direta (SQLite + PHP Built-in Server)
O projeto está configurado por padrão para utilizar **SQLite**, dispensando servidor MySQL externo para desenvolvimento e avaliação rápida.

```powershell
# 1. Copiar o arquivo de configuração de ambiente (caso não exista)
copy .env.example .env

# 2. Criar o arquivo de banco SQLite (caso não exista)
type nul > database\database.sqlite

# 3. Instalar dependências do Composer
composer install

# 4. Executar migrações e popular dados iniciais
php artisan migrate:fresh --seed

# 5. Iniciar o servidor na porta 8000
php artisan serve --host=127.0.0.1 --port=8000
```
*(No Windows, também é possível executar diretamente o arquivo `iniciar.bat`).*

### Opção 2: Execução via Docker (Nginx + PHP-FPM + MySQL 5.7 + phpMyAdmin)
Caso deseje rodar a infraestrutura completa em containers:

```bash
docker-compose up -d --build
docker-compose exec app composer install --optimize-autoloader
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate:fresh --seed
```
- **Aplicação**: `http://localhost:8000`
- **phpMyAdmin**: `http://localhost:8080` (Servidor: `db` | Usuário: `clinica_user` | Senha: `clinica_senha_forte`)

---

## Credenciais de Acesso (Dados Iniciais / Seeders)

A senha padrão para todos os usuários pré-cadastrados é **`password`**.

| Perfil | Nome | E-mail | Registro / Função |
| :--- | :--- | :--- | :--- |
| **Direção Clínica (Admin)** | Dr. Andrik Cardoso | `admin@clinicaandrik.com.br` | CRM/PB 8412 - Clínica Geral & Gestão |
| **Médico** | Dr. Juliane | `dr.juliane@clinicaandrik.com.br` | CRM/PB 9154 - Pediatria & Clínica Geral |
| **Médico** | Dr. Pedro Miguel | `pedro.miguel@clinicaandrik.com.br` | CRM/PB 7820 - Cardiologia |
| **Médico** | Dr. Mauricio Cunha | `mauricio.cunha@clinicaandrik.com.br` | CRM/PB 6531 - Dermatologia |
| **Médico** | Dr. Vinicius Oliveira | `vinicius.oliveira@clinicaandrik.com.br` | CRM/PB 11204 - Ortopedia e Traumatologia |
| **Médico** | Dr. Matheus Lins | `matheus.lins@clinicaandrik.com.br` | CRM/PB 12480 - Oftalmologia |
| **Médico** | Dr. Maycon | `maycon@clinicaandrik.com.br` | CRM/PB 13952 - Neurologia |
| **Recepção** | Matheus Brito | `matheus.brito@clinicaandrik.com.br` | Recepção & Atendimento ao Paciente |
| **Recepção** | Ozair | `ozair@clinicaandrik.com.br` | Recepção & Triagem |

---

## Executando os Testes Automatizados

A suíte de testes (`tests/Feature/ClinicaAndrikTest.php`) utiliza SQLite em memória e valida autenticação, controle de acesso por perfil, validação de CPF e endereço, conflito de agenda, cálculo de IMC e relatórios:

```bash
vendor/bin/phpunit
```

---

## Estrutura do Projeto

```text
ClinicaAndrik/
├── app/
│   ├── Consulta.php                        # Model de Consulta e escopos de agenda
│   ├── Paciente.php                        # Model de Paciente e validação de CPF (Módulo 11)
│   ├── Prontuario.php                      # Model de Prontuário e cálculo de IMC
│   ├── User.php                            # Model de Usuário e perfis de acesso
│   └── Http/
│       ├── Controllers/
│       │   ├── AuthController.php          # Login, logout e perfil
│       │   ├── ConsultaController.php      # Agendamento e validação de conflitos
│       │   ├── DashboardController.php     # Indicadores e agenda diária
│       │   ├── HistoricoController.php     # Linha do tempo clínica do paciente
│       │   ├── PacienteController.php      # CRUD de pacientes e validações cadastrais
│       │   ├── ProntuarioController.php    # Registro clínico e sinais vitais
│       │   ├── RelatorioController.php     # Relatórios gerenciais e filtros
│       │   └── UsuarioController.php       # Gestão de profissionais e equipe
│       └── Middleware/
│           └── CheckRole.php               # Controle de permissões por perfil
├── database/
│   ├── migrations/                         # Estrutura das tabelas (users, pacientes, consultas, prontuarios)
│   └── seeds/                              # População inicial de usuários, pacientes, consultas e prontuários
├── public/
│   ├── css/clinica.css                     # Folha de estilo institucional (visual retilíneo)
│   └── js/                                 # Scripts base (jQuery e Bootstrap)
├── resources/views/                        # Templates Blade organizados por módulo
├── routes/web.php                          # Definição das rotas web autenticadas
├── tests/Feature/ClinicaAndrikTest.php     # Testes funcionais automatizados
└── Documentacao_Migrations_e_Fluxo_Clinica_Andrik.pdf # Guia em PDF das Migrations e Fluxo Passo a Passo
```
