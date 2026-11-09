<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Paciente;
use App\Services\CorreiosCepService;

class PacienteController extends Controller
{
    public const ESTADOS_UF = [
        'AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG',
        'PA','PB','PR','PE','PI','RJ','RN','RS','SC','SP','SE','TO','RO','RR'
    ];

    public const CIDADES_POR_UF = [
        'AC' => ['Rio Branco', 'Cruzeiro do Sul', 'Sena Madureira', 'Tarauacá', 'Feijó'],
        'AL' => ['Maceió', 'Arapiraca', 'Rio Largo', 'Palmeira dos Índios', 'União dos Palmares', 'Penedo'],
        'AP' => ['Macapá', 'Santana', 'Laranjal do Jari', 'Oiapoque', 'Mazagão'],
        'AM' => ['Manaus', 'Parintins', 'Itacoatiara', 'Manacapuru', 'Coari', 'Tefé'],
        'BA' => ['Salvador', 'Feira de Santana', 'Vitória da Conquista', 'Camaçari', 'Itabuna', 'Juazeiro', 'Lauro de Freitas', 'Ilhéus', 'Jequié', 'Teixeira de Freitas', 'Barreiras', 'Alagoinhas', 'Porto Seguro'],
        'CE' => ['Fortaleza', 'Caucaia', 'Juazeiro do Norte', 'Maracanaú', 'Sobral', 'Crato', 'Itapipoca', 'Maranguape', 'Iguatu', 'Quixadá'],
        'DF' => ['Brasília', 'Taguatinga', 'Ceilândia', 'Samambaia', 'Plano Piloto', 'Águas Claras', 'Gama', 'Guará'],
        'ES' => ['Vitória', 'Vila Velha', 'Serra', 'Cariacica', 'Cachoeiro de Itapemirim', 'Linhares', 'São Mateus', 'Colatina', 'Guarapari'],
        'GO' => ['Goiânia', 'Aparecida de Goiânia', 'Anápolis', 'Rio Verde', 'Luziânia', 'Águas Lindas de Goiás', 'Valparaíso de Goiás', 'Trindade', 'Formosa', 'Itumbiara'],
        'MA' => ['São Luís', 'Imperatriz', 'São José de Ribamar', 'Timon', 'Caxias', 'Codó', 'Paço do Lumiar', 'Açailândia', 'Bacabal'],
        'MT' => ['Cuiabá', 'Várzea Grande', 'Rondonópolis', 'Sinop', 'Tangará da Serra', 'Cáceres', 'Sorriso', 'Lucas do Rio Verde', 'Primavera do Leste'],
        'MS' => ['Campo Grande', 'Dourados', 'Três Lagoas', 'Corumbá', 'Ponta Porã', 'Sidrolândia', 'Naviraí', 'Nova Andradina'],
        'MG' => ['Belo Horizonte', 'Uberlândia', 'Contagem', 'Juiz de Fora', 'Betim', 'Montes Claros', 'Ribeirão das Neves', 'Uberaba', 'Governador Valadares', 'Ipatinga', 'Sete Lagoas', 'Divinópolis', 'Santa Luzia', 'Poços de Caldas', 'Patos de Minas', 'Pouso Alegre'],
        'PA' => ['Belém', 'Ananindeua', 'Santarém', 'Marabá', 'Parauapebas', 'Castanhal', 'Abaetetuba', 'Cametá', 'Marituba', 'Bragança'],
        'PB' => ['João Pessoa', 'Campina Grande', 'Santa Rita', 'Patos', 'Bayeux', 'Sousa', 'Cabedelo', 'Cajazeiras', 'Guarabira', 'Sapé', 'Mamanguape', 'Queimadas', 'São Bento', 'Monteiro', 'Esperança', 'Pombal', 'Catolé do Rocha', 'Conde', 'Alhandra', 'Pedras de Fogo', 'Itabaiana', 'Solânea', 'Bananeiras', 'Areia'],
        'PR' => ['Curitiba', 'Londrina', 'Maringá', 'Ponta Grossa', 'Cascavel', 'São José dos Pinhais', 'Foz do Iguaçu', 'Colombo', 'Guarapuava', 'Paranaguá', 'Araucária', 'Toledo', 'Apucarana', 'Pinhais'],
        'PE' => ['Recife', 'Jaboatão dos Guararapes', 'Olinda', 'Caruaru', 'Petrolina', 'Paulista', 'Cabo de Santo Agostinho', 'Camaragibe', 'Garanhuns', 'Vitória de Santo Antão', 'Igarassu', 'São Lourenço da Mata', 'Goiana', 'Arcoverde', 'Serra Talhada'],
        'PI' => ['Teresina', 'Parnaíba', 'Picos', 'Piripiri', 'Floriano', 'Campo Maior', 'Barras'],
        'RJ' => ['Rio de Janeiro', 'São Gonçalo', 'Duque de Caxias', 'Nova Iguaçu', 'Niterói', 'Belford Roxo', 'Campos dos Goytacazes', 'São João de Meriti', 'Petrópolis', 'Volta Redonda', 'Macaé', 'Magé', 'Itaboraí', 'Cabo Frio', 'Angra dos Reis', 'Nova Friburgo', 'Teresópolis'],
        'RN' => ['Natal', 'Mossoró', 'Parnamirim', 'São Gonçalo do Amarante', 'Macaíba', 'Ceará-Mirim', 'Caicó', 'Assu', 'Currais Novos', 'São José de Mipibu', 'Santa Cruz', 'Nova Cruz'],
        'RS' => ['Porto Alegre', 'Caxias do Sul', 'Canoas', 'Pelotas', 'Santa Maria', 'Gravataí', 'Viamão', 'Novo Hamburgo', 'São Leopoldo', 'Rio Grande', 'Alvorada', 'Passo Fundo', 'Sapucaia do Sul', 'Uruguaiana'],
        'RO' => ['Porto Velho', 'Ji-Paraná', 'Ariquemes', 'Vilhena', 'Cacoal', 'Rolim de Moura', 'Jaru'],
        'RR' => ['Boa Vista', 'Rorainópolis', 'Caracaraí', 'Alto Alegre', 'Mucajaí'],
        'SC' => ['Florianópolis', 'Joinville', 'Blumenau', 'São José', 'Chapecó', 'Itajaí', 'Criciúma', 'Jaraguá do Sul', 'Palhoça', 'Lages', 'Balneário Camboriú', 'Brusque', 'Tubarão'],
        'SP' => ['São Paulo', 'Guarulhos', 'Campinas', 'São Bernardo do Campo', 'Santo André', 'São José dos Campos', 'Osasco', 'Ribeirão Preto', 'Sorocaba', 'Mauá', 'São José do Rio Preto', 'Mogi das Cruzes', 'Santos', 'Diadema', 'Jundiaí', 'Piracicaba', 'Carapicuíba', 'Bauru', 'Itaquaquecetuba', 'São Vicente', 'Franca', 'Praia Grande', 'Guarujá', 'Taubaté', 'Limeira', 'Suzano', 'Barueri'],
        'SE' => ['Aracaju', 'Nossa Senhora do Socorro', 'Lagarto', 'Itabaiana', 'São Cristóvão', 'Estância', 'Tobias Barreto'],
        'TO' => ['Palmas', 'Araguaína', 'Gurupi', 'Porto Nacional', 'Paraíso do Tocantins', 'Colinas do Tocantins'],
    ];

    public static function listarCidadesUf(?string $uf, ?string $cidadeAtual = null): array
    {
        $uf = strtoupper(trim((string) $uf));
        $cidades = self::CIDADES_POR_UF[$uf] ?? [];
        $cidadeAtual = trim((string) $cidadeAtual);

        if ($cidadeAtual !== '') {
            $existe = false;
            foreach ($cidades as $c) {
                if (mb_strtolower($c, 'UTF-8') === mb_strtolower($cidadeAtual, 'UTF-8')) {
                    $existe = true;
                    break;
                }
            }
            if (!$existe) {
                array_unshift($cidades, $cidadeAtual);
            }
        }

        return $cidades;
    }

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $busca = $request->input('busca');
        $convenio = $request->input('convenio');

        $query = Paciente::withCount(['consultas', 'prontuarios'])->buscar($busca);

        if (!empty($convenio)) {
            $query->where('convenio', $convenio);
        }

        $pacientes = $query->orderBy('nome', 'asc')->paginate(12)->appends($request->query());
        $convenios = Paciente::listarConvenios();

        return view('pacientes.index', compact('pacientes', 'busca', 'convenio', 'convenios'));
    }

    public function salvarConvenio(Request $request)
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'min:2', 'max:50', 'regex:/^[\pL0-9\s\.\-\&\+]+$/u'],
        ], [
            'nome.required' => 'Informe o nome do convênio.',
            'nome.min' => 'O nome do convênio deve ter pelo menos 2 caracteres.',
            'nome.regex' => 'O nome do convênio contém caracteres inválidos.',
        ]);

        if (strpos(trim($dados['nome']), '-') === 0) {
            return response()->json(['erro' => 'Nome de convênio inválido.'], 422);
        }

        $convenio = Paciente::registrarConvenio($dados['nome']);

        return response()->json([
            'sucesso' => true,
            'convenio' => $convenio,
            'convenios' => Paciente::listarConvenios(),
        ]);
    }

    public function create()
    {
        return view('pacientes.create');
    }

    public function store(Request $request)
    {
        $data = $this->validarPaciente($request);
        if (!empty($data['convenio'])) {
            Paciente::registrarConvenio($data['convenio']);
        }
        $paciente = Paciente::create($data);

        return redirect()->route('pacientes.show', $paciente->id)
            ->with('success', 'Paciente cadastrado com sucesso.');
    }

    public function show($id)
    {
        $paciente = Paciente::with(['consultas.medico', 'prontuarios.medico'])->findOrFail($id);
        return view('pacientes.show', compact('paciente'));
    }

    public function edit($id)
    {
        $paciente = Paciente::findOrFail($id);
        return view('pacientes.edit', compact('paciente'));
    }

    public function update(Request $request, $id)
    {
        $paciente = Paciente::findOrFail($id);
        $data = $this->validarPaciente($request, $paciente->id);
        if (!empty($data['convenio'])) {
            Paciente::registrarConvenio($data['convenio']);
        }
        $paciente->update($data);

        return redirect()->route('pacientes.show', $paciente->id)
            ->with('success', 'Cadastro do paciente atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $paciente = Paciente::findOrFail($id);
        $nome = $paciente->nome;
        $paciente->delete();

        return redirect()->route('pacientes.index')
            ->with('success', "Paciente {$nome} removido do sistema.");
    }

    public function consultarCep($cep, CorreiosCepService $cepService)
    {
        $resultado = $cepService->consultar((string) $cep);
        $status = ($resultado['encontrado'] || !empty($resultado['offline'])) ? 200 : 422;

        return response()->json($resultado, $status);
    }

    protected function validarPaciente(Request $request, $ignorarId = null)
    {
        $cpfBruto = trim((string) $request->input('cpf', ''));
        if ($cpfBruto !== '' && strpos($cpfBruto, '-') !== 0) {
            $request->merge(['cpf' => Paciente::formatarCpf($cpfBruto)]);
        }

        $cepBruto = trim((string) $request->input('cep', ''));
        $dadosCepCorreios = null;

        if ($cepBruto !== '' && strpos($cepBruto, '-') !== 0) {
            $cepDigitos = preg_replace('/[^0-9]/', '', $cepBruto);
            if (strlen($cepDigitos) === 8) {
                $request->merge(['cep' => substr($cepDigitos, 0, 5) . '-' . substr($cepDigitos, 5, 3)]);
                $dadosCepCorreios = app(CorreiosCepService::class)->consultar($cepDigitos);

                if (!empty($dadosCepCorreios['encontrado'])) {
                    if (!$request->filled('estado')) {
                        $request->merge(['estado' => $dadosCepCorreios['uf']]);
                    }
                    if (!$request->filled('cidade')) {
                        $request->merge(['cidade' => $dadosCepCorreios['cidade']]);
                    }
                    if (!$request->filled('endereco') && !empty($dadosCepCorreios['endereco'])) {
                        $request->merge(['endereco' => $dadosCepCorreios['endereco']]);
                    }
                    if (!$request->filled('bairro') && !empty($dadosCepCorreios['bairro'])) {
                        $request->merge(['bairro' => $dadosCepCorreios['bairro']]);
                    }
                }
            }
        }

        if ($request->filled('estado')) {
            $request->merge(['estado' => strtoupper(trim($request->input('estado')))]);
        }

        $uniqueCpf = 'unique:pacientes,cpf' . ($ignorarId ? ',' . $ignorarId : '');
        $ufs = implode(',', self::ESTADOS_UF);

        return $request->validate([
            'nome' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[\pL\s\'\-\.]+$/u'],
            'cpf' => [
                'required',
                'string',
                $uniqueCpf,
                function ($attribute, $value, $fail) {
                    $v = trim((string) $value);
                    if (strpos($v, '-') === 0) {
                        $fail('Não são permitidos números negativos no CPF.');
                    } elseif (strlen($v) !== 14 || !Paciente::validarCpf($v)) {
                        $fail('O CPF informado é inválido. Verifique os 11 dígitos.');
                    }
                },
            ],
            'rg' => [
                'nullable',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $v = trim((string) $value);
                    if (strpos($v, '-') === 0) {
                        $fail('Não são permitidos valores negativos no RG.');
                    } elseif (!preg_match('/^[0-9A-Za-z][0-9A-Za-z\.\-\s]{3,19}$/', $v)) {
                        $fail('Informe um número de RG válido (sem símbolos especiais).');
                    }
                },
            ],
            'data_nascimento' => ['required', 'date', 'before:today', 'after:1900-01-01'],
            'sexo' => ['required', 'in:M,F,Outro'],
            'telefone' => ['required', 'string', 'max:20', 'regex:/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'tipo_sanguineo' => ['nullable', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'convenio' => ['nullable', 'string', 'max:50'],
            'numero_carteirinha' => [
                'nullable',
                'string',
                'max:50',
                function ($attribute, $value, $fail) {
                    $v = trim((string) $value);
                    if (strpos($v, '-') === 0) {
                        $fail('Não são permitidos valores negativos no número da carteirinha.');
                    } elseif (!preg_match('/^[0-9A-Za-z][0-9A-Za-z\.\-\s\/]*$/', $v)) {
                        $fail('O número da carteirinha deve conter apenas números e letras.');
                    }
                },
            ],
            'alergias' => ['nullable', 'string', 'max:1000'],
            'medicamentos_uso_continuo' => ['nullable', 'string', 'max:1000'],
            'historico_cirurgico' => ['nullable', 'string', 'max:1000'],
            'contato_emergencia_nome' => ['nullable', 'string', 'max:100', 'regex:/^[\pL\s\'\-\.\(\)]+$/u'],
            'contato_emergencia_telefone' => ['nullable', 'string', 'max:20', 'regex:/^\(?\d{2}\)?\s?\d{4,5}\-?\d{4}$/'],
            'cep' => [
                'nullable',
                'string',
                'max:9',
                function ($attribute, $value, $fail) use ($dadosCepCorreios) {
                    $v = trim((string) $value);
                    if (strpos($v, '-') === 0) {
                        $fail('Não são permitidos números negativos no CEP.');
                    } elseif (!preg_match('/^\d{5}\-?\d{3}$/', $v)) {
                        $fail('O CEP deve estar no formato 00000-000 (8 dígitos positivos).');
                    } elseif (is_array($dadosCepCorreios) && empty($dadosCepCorreios['offline']) && empty($dadosCepCorreios['encontrado'])) {
                        $fail($dadosCepCorreios['mensagem'] ?? 'O CEP informado não existe na base dos Correios.');
                    }
                },
            ],
            'endereco' => ['nullable', 'string', 'max:255'],
            'numero' => [
                'nullable',
                'string',
                'max:10',
                function ($attribute, $value, $fail) {
                    $v = trim((string) $value);
                    if (strpos($v, '-') === 0) {
                        $fail('Não são permitidos números negativos no endereço.');
                    } elseif (!preg_match('/^\d{1,10}$/', $v)) {
                        $fail('O número do endereço deve conter apenas algarismos numéricos.');
                    } elseif ((int) $v <= 0) {
                        $fail('O número do endereço deve ser um valor maior que zero.');
                    }
                },
            ],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => [
                'nullable',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($dadosCepCorreios) {
                    if (is_array($dadosCepCorreios) && !empty($dadosCepCorreios['encontrado'])) {
                        $cidadeInformada = CorreiosCepService::normalizarTexto($value);
                        $cidadeCorreios = CorreiosCepService::normalizarTexto($dadosCepCorreios['cidade']);
                        if ($cidadeInformada !== '' && $cidadeInformada !== $cidadeCorreios) {
                            $fail("A cidade informada ({$value}) não corresponde ao CEP nos Correios ({$dadosCepCorreios['cidade']}).");
                        }
                    }
                },
            ],
            'estado' => [
                'nullable',
                'string',
                'size:2',
                "in:{$ufs}",
                function ($attribute, $value, $fail) use ($dadosCepCorreios) {
                    if (is_array($dadosCepCorreios) && !empty($dadosCepCorreios['encontrado'])) {
                        $ufInformada = strtoupper(trim((string) $value));
                        if ($ufInformada !== '' && $ufInformada !== $dadosCepCorreios['uf']) {
                            $fail("A UF informada ({$ufInformada}) não corresponde ao CEP nos Correios ({$dadosCepCorreios['uf']}).");
                        }
                    }
                },
            ],
            'observacoes' => ['nullable', 'string', 'max:1000'],
        ], [
            'nome.required' => 'O nome do paciente é obrigatório.',
            'nome.min' => 'O nome deve ter no mínimo 3 caracteres.',
            'nome.regex' => 'O nome do paciente deve conter apenas letras e espaços.',
            'cpf.required' => 'O CPF é obrigatório.',
            'cpf.unique' => 'Este CPF já está cadastrado para outro paciente.',
            'data_nascimento.required' => 'A data de nascimento é obrigatória.',
            'data_nascimento.before' => 'A data de nascimento deve ser anterior a hoje.',
            'data_nascimento.after' => 'Informe uma data de nascimento válida (após 1900).',
            'telefone.required' => 'O telefone de contato é obrigatório.',
            'telefone.regex' => 'Informe um telefone válido com DDD (sem valores negativos). Ex: (83) 98888-7777.',
            'email.email' => 'Informe um endereço de e-mail válido.',
            'contato_emergencia_nome.regex' => 'O nome do contato de emergência deve conter apenas letras.',
            'contato_emergencia_telefone.regex' => 'Informe um telefone de emergência válido com DDD.',
            'estado.size' => 'A UF do estado deve ter 2 letras.',
            'estado.in' => 'Informe uma sigla de UF brasileira válida (ex: PB, SP, RJ).',
        ]);
    }
}
