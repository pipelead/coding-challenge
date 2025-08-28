# Sistema de Comunicação Multi‑Canais (Laravel 12 + Vue 3 + Inertia)

## Visão Geral

Aplicação de atendimento com conversas de contatos em canais simulados (WhatsApp, Messenger, Email). Sem integrações reais: envio simulado com persistência, jobs assíncronos, polling otimizado e virtualização do histórico.

## Stack

- Backend: Laravel 12 (PHP 8.2+)
- Frontend: Vue 3 (Composition API) + Inertia.js + Tailwind CSS
- Banco: SQLite (padrão) ou MySQL
- Fila: database queue
- Testes: Pest

---

## 1) Instalação

Requisitos: PHP 8.2+, Composer, Node 18+, npm.

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

Banco SQLite (padrão):

```bash
# criar arquivo do banco
mkdir -p database && touch database/database.sqlite

# .env (exemplos relevantes)
DB_CONNECTION=sqlite
QUEUE_CONNECTION=database
APP_ENV=local
```

Frontend (dev server) ou build:

```bash
npm run dev
# ou build de produção
npm run build
```

---

### 2) Migrações e Seeders

Criar tabelas e semear canais padrão:

```bash
php artisan migrate
php artisan db:seed
```

Isso executa o `ChannelSeeder` e cria os canais: whatsapp, messenger e email.

---

## 3) Geração de Mensagens Fake

Comando Artisan para popular contatos e mensagens (opcionalmente enfileirar envios "out"):

```bash
php artisan messages:generate {contacts=10} {per=20} {--dispatch}

# exemplo: 10 contatos, 30 mensagens cada, com disparo de jobs para as "out"
php artisan messages:generate 10 30 --dispatch
```

---

## 4) Fila/Jobs (envio simulado)

- Configure `QUEUE_CONNECTION=database` no `.env`.
- Inicie o worker para processar envios:

```bash
php artisan queue:work
```

- Job: `App/Jobs/SendMessageJob` com tentativas (`$tries = 3`) e backoff `[5,10,30]`.
- Serviços de canal em `App/Services/Channels/*Sender` simulam delay (1–3s), registram logs e podem lançar erro para simular falhas.

---

## 5) Executando o Projeto

Servidor Laravel:

```bash
php artisan serve
```

Acessos principais:

- `GET /` lista contatos (sidebar com preview e não lidas)
- `GET /conversations/{contact}` mostra histórico com:
- Virtualização/paginação do histórico
- Agrupamento por data
- Status visual (enviando/sent/failed)
- Timestamps por mensagem
- `POST /messages` envia mensagem (cria com `status=sending` e dispara `SendMessageJob`)
- Polling otimizado para novas mensagens (parcial via Inertia)
- Endpoint provisório de falhas: `GET /admin/messages/failed`

---

## 6) Testes (Pest)

Executar testes:

```bash
./vendor/bin/pest
```

Cobertura básica:

- Criação de mensagem persiste no banco
- `SendMessageJob` marca como `sent` em sucesso
- Polling (`/conversations/{contact}/messages/updates`) retorna apenas novas mensagens após `last_id`

---

## 7) Arquitetura e Decisões Técnicas

- KISS/YAGNI: foco em clareza, sem abstrações prematuras.
- Inertia ao invés de REST explícito.
- Serviços de canal via interface `ChannelSenderInterface` (WhatsApp, Messenger, Email) em `App/Services/Channels/`.
- Envio assíncrono isolado no job; `SenderResolver` para resolver sender por `channel.slug` (facilita testes e DI).
- Frontend: Vue 3 + Inertia, Tailwind simples, componentes:
    - `ContactList.vue`, `ConversationView.vue` (virtualizado, agrupamento por data, status e timestamps), `MessageInput.vue` (select de canal, textarea, spinner/disabled).
- Polling incremental: consulta endpoint de updates por `last_id` e aplica Inertia partial reload (`only: ['messages']`).
- Banco: índices em `messages(contact_id, created_at)` e `messages(status)`; eager loading seletivo.
- Cache leve (10s) para lista de contatos.
- Pinia apenas para estado mínimo (contatos, conversa ativa, mensagens).

---

## 8) Possíveis Melhorias Futuras

- Dark mode (Tailwind class strategy) e toasts para erros.
- Auto‑scroll mais inteligente (não pular enquanto o usuário lê histórico).
- SSE/WebSockets (Laravel Reverb/Echo) para near‑realtime.
- Observabilidade (Sentry/Scout) e tela/admin para falhas.
- Paginação com cursor e otimizações adicionais.
- Acessibilidade (ARIA, teclado) mais ampla.

---

### Estrutura (referência rápida)

- Migrations/Models: `contacts`, `channels`, `messages` com relações adequadas.
- Services: `App/Services/Channels/*Sender` + `SenderResolver`.
- Job: `App/Jobs/SendMessageJob`.
- Controllers: `ConversationController`, `MessageController`, `ContactController`.
- Rotas: `routes/web.php` (Inertia pages + polling + admin failed).
- Frontend: `resources/js/pages/Conversations/*` e `resources/js/Components/*`.
- Command: `messages:generate`.
