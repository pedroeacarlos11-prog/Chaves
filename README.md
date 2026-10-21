# Controle de Chaves

Aplicação Laravel para cadastrar funcionários e chaves, registrar empréstimos e acompanhar devoluções. As telas de funcionários, chaves e empréstimos leem e gravam os registros do banco; a interface usa Bootstrap 5 e funciona em telas menores.

## Tecnologias

- PHP 8.4 e Laravel 13
- MySQL
- Bootstrap 5.3 e Bootstrap Icons
- Vite para compilar os assets do projeto
- Sanctum para tokens da API

## Requisitos

- PHP 8.3 ou superior, com as extensões exigidas pelo Laravel e pelo driver MySQL
- Composer
- MySQL 8 ou compatível
- Node.js e npm para compilar os assets
- Laravel Herd para acessar o site local em `http://chaves.test`

## Instalação

Instale as dependências e prepare o ambiente:

```bash
composer install
cp .env.example .env
php artisan key:generate
npm install
npm run build
```

No Windows PowerShell, copie o arquivo de ambiente com:

```powershell
Copy-Item .env.example .env
```

Configure no `.env` as credenciais do MySQL. Para uma instalação nova, `DB_CONNECTION_LARAVEL` e `DB_DATABASE_LARAVEL` devem apontar para o mesmo MySQL e banco definidos por `DB_CONNECTION` e `DB_DATABASE`. O modelo de `.env.example` usa `chaves_laravel` nos dois nomes de conexão para que os models da API e as migrations usem o mesmo banco.

Crie o banco antes de migrar:

```sql
CREATE DATABASE chaves_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Em seguida, aplique as migrations:

```bash
php artisan migrate
```

O Herd já serve o projeto em `http://chaves.test`; não é necessário iniciar `php artisan serve`. Para recompilar os assets enquanto trabalha na interface, use `npm run dev` em outro terminal.

### Atenção ao banco já existente

O `.env` usado neste ambiente mantém dois bancos: `chaves_laravel` para usuários web, funcionários, chaves e empréstimos; `laravel` para contas da API, tokens e retiradas. Há dados distintos nas duas tabelas `usuario`. As migrations atuais de `token_usuario` e `retiradas` aparecem pendentes em `chaves_laravel`, embora tabelas com esses nomes já existam em `laravel`.

Não rode `php artisan migrate` no ambiente atual antes de planejar a consolidação desses dados: o comando criaria tabelas vazias no banco principal, sem transferir os registros existentes. Nenhuma migration foi executada nesta revisão. Consulte `php artisan migrate:status` e faça uma cópia de segurança antes de alterar essa configuração.

## Autenticação

As telas web usam o login de sessão em `/login` e o cadastro em `/cadastro`. A API usa contas do model `Usuario`, que são independentes dos usuários web do model `User` no ambiente atual. Para obter um token, envie:

```http
POST /api/v1/login
Accept: application/json
Content-Type: application/json
```

```json
{
  "email": "conta-da-api@example.test",
  "senha": "sua-senha",
  "device_name": "Thunder Client"
}
```

A resposta inclui `access_token`, `token_type` e `expires_at`. Use `Authorization: Bearer <access_token>` nos outros endpoints. `POST /api/v1/logout` revoga o token atual.

## API REST v1

Todos os recursos abaixo exigem autenticação Bearer, exceto o login. Listagens são paginadas. `PUT` valida a representação completa do recurso; `PATCH` aceita apenas os campos enviados. Respostas de listagem e consulta usam recursos JSON com a chave `data`.

| Recurso | Listar | Consultar | Criar | Atualizar | Excluir |
|---|---|---|---|---|---|
| Funcionários | `GET /api/v1/funcionarios` | `GET /api/v1/funcionarios/{id}` | `POST /api/v1/funcionarios` | `PUT/PATCH /api/v1/funcionarios/{id}` | `DELETE /api/v1/funcionarios/{id}` |
| Chaves | `GET /api/v1/chaves` | `GET /api/v1/chaves/{id}` | `POST /api/v1/chaves` | `PUT/PATCH /api/v1/chaves/{id}` | `DELETE /api/v1/chaves/{id}` |
| Empréstimos | `GET /api/v1/emprestimos` | `GET /api/v1/emprestimos/{id}` | `POST /api/v1/emprestimos` | `PUT/PATCH /api/v1/emprestimos/{id}` | `DELETE /api/v1/emprestimos/{id}` |
| Usuários da API | `GET /api/v1/usuarios` | `GET /api/v1/usuarios/{id}` | `POST /api/v1/usuarios` | `PUT/PATCH /api/v1/usuarios/{id}` | `DELETE /api/v1/usuarios/{id}` |
| Retiradas do usuário autenticado | `GET /api/v1/retiradas` | `GET /api/v1/retiradas/{id}` | `POST /api/v1/retiradas` | `PUT/PATCH /api/v1/retiradas/{id}` | `DELETE /api/v1/retiradas/{id}` |
| Tokens do usuário autenticado | `GET /api/v1/tokens-usuario` | `GET /api/v1/tokens-usuario/{id}` | `POST /api/v1/tokens-usuario` | `PUT/PATCH /api/v1/tokens-usuario/{id}` | `DELETE /api/v1/tokens-usuario/{id}` |
| Logs de empréstimos | `GET /api/v1/logs-emprestimos` | `GET /api/v1/logs-emprestimos/{id}` | `POST /api/v1/logs-emprestimos` | `PUT/PATCH /api/v1/logs-emprestimos/{id}` | `DELETE /api/v1/logs-emprestimos/{id}` |

Também existe `GET /api/v1/ver_usuario?id={id}` para compatibilidade com a tela antiga. Retiradas e tokens são limitados à conta autenticada. O token em texto puro só aparece na resposta de login ou na criação de token; as respostas de listagem não o exibem.

Cada recurso também possui `GET /api/v1/{recurso}/simples`, uma listagem paginada com campos essenciais para seletores e telas compactas. A lista simples de usuários retorna apenas ID, nome e e-mail; a de tokens não inclui o valor do token. Retiradas e tokens continuam limitados à conta autenticada.

Para compatibilidade com o padrão de nomes do controller, também há rotas como `POST /api/v1/salvar_usuario`, `GET /api/v1/ver_usuario?id={id}`, `GET /api/v1/listar_usuarios`, `GET /api/v1/listar_usuarios_simples`, `PUT/PATCH /api/v1/alterar_usuario` e `DELETE /api/v1/deletar_usuario`. Os mesmos nomes estão disponíveis para funcionários, chaves, empréstimos, logs, retiradas e tokens; `PUT`, `PATCH` e `DELETE` recebem `id` no corpo JSON. Todas exigem token Bearer.

### Payloads de exemplo

Criar funcionário:

```http
POST /api/v1/funcionarios
Accept: application/json
Content-Type: application/json
Authorization: Bearer <access_token>
```

```json
{
  "nome": "Funcionário de teste",
  "email": "funcionario@example.test",
  "cargo": "Técnico"
}
```

Criar empréstimo (a chave deve estar disponível):

```json
{
  "funcionario_id": 1,
  "chave_id": 1,
  "data_retirada": "2026-10-19",
  "hora_retirada": "08:30"
}
```

Uma devolução atualiza o empréstimo com `status: "Devolvido"`, `data_devolucao` e `hora_devolucao`. A chave volta para `Disponível` na mesma transação.

### Códigos de resposta

- `200`: consulta ou atualização concluída
- `201`: registro criado
- `204`: exclusão concluída, sem corpo
- `401`: token ausente ou inválido
- `404`: registro inexistente ou fora do escopo da conta
- `409`: exclusão bloqueada por relacionamentos ou conflito de disponibilidade
- `422`: validação recusada; o JSON inclui `message` e `errors`
- `500`: erro inesperado; detalhes internos não são retornados pela API

Exemplo de validação recusada (`422`):

```json
{
  "message": "The nome field is required.",
  "errors": {
    "nome": ["The nome field is required."]
  }
}
```

## Interface web

- `/`: resumo calculado a partir do banco
- `/agenda`: lista de retiradas registradas, ordenada pela data existente no empréstimo
- `/emprestimos`: pesquisa, cadastro, edição, devolução e exclusão de empréstimos
- `/funcionarios`: pesquisa e CRUD de funcionários
- `/chaves`: pesquisa, filtro por situação e CRUD de chaves

Os formulários incluem validação do Laravel, alertas de sucesso/erro e confirmação antes de excluir. Registros vinculados a empréstimos não podem ser removidos; empréstimos com logs não podem ser excluídos.

## Testes

Execute a suíte PHPUnit:

```bash
php artisan test --compact
```

Os testes usam SQLite em memória conforme `phpunit.xml`; não escrevem nos bancos MySQL locais.

## Thunder Client

A coleção fica em `thunder-tests/collections/tc_col_chaves_api.json`; o modelo do ambiente fica em `thunder-tests/environments/chaves.env.example`. Copie-o para `chaves.local.env`, que está no `.gitignore`, e preencha as credenciais e IDs de teste.

Ative o armazenamento de coleções no workspace do Thunder Client para carregar a pasta `thunder-tests`. Crie um ambiente na extensão, vincule-o ao arquivo `.env` local e selecione-o como ativo. O Thunder Client usa variáveis no formato `{{nome}}`; consulte a [documentação oficial de ambientes](https://docs.thunderclient.com/features/environments). A importação de coleções pela interface depende da edição do Thunder Client, conforme a [documentação oficial](https://docs.thunderclient.com/features/import).

Antes de executar a coleção:

1. Preencha `loginEmail` e `loginSenha` no ambiente com uma conta existente da API; a conta do formulário web não autentica a API.
2. Execute `Login` e copie `access_token` para `token`.
3. Crie funcionário e chave de teste e copie os IDs para `funcionarioId` e `chaveId` antes de criar empréstimo.
4. Atualize as demais variáveis de ID após cada criação. O valor inicial `0` evita consultar um registro real por engano.
5. Use os requests DELETE somente com os IDs dos registros de teste.

Os endpoints foram verificados pela suíte Laravel. A coleção está preparada, mas não foi executada pela extensão Thunder Client nesta sessão; ela depende de uma conta de API disponível no banco local.
