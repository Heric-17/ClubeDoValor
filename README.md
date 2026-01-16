# Sistema de Gestão de Investimentos

Sistema desenvolvido em Laravel, Vue.js e Inertia para gestão de investimentos e clientes.

## 🚀 Tecnologias

### Backend
- **PHP:** 8.2+
- **Laravel:** 12.0
- **Banco de Dados:** MySQL 8.0
- **Cache/Sessões:** Redis (opcional, configurável)

### Frontend
- **Vue.js:** 3.5+
- **TypeScript:** 5.2+
- **Inertia.js:** 2.0
- **UI:** Tailwind CSS 4.1 + shadcn-vue (reka-ui)
- **Build Tool:** Vite 7.0

### Ferramentas de Desenvolvimento
- **Laravel Pint:** Code style (PHP)
- **ESLint + Prettier:** Code style (JavaScript/TypeScript)
- **PHPUnit:** Testes unitários e de integração
- **Playwright:** Testes E2E (via GitHub Actions)

## 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer
- Node.js 22+ e npm
- MySQL 8.0+
- Redis (opcional, mas recomendado)

## 🔧 Instalação

### 1. Clone o repositório

```bash
git clone <repository-url>
cd example-app
```

### 2. Instale as dependências

```bash
# Dependências PHP
composer install

# Dependências Node.js
npm install
```

### 3. Configure o ambiente

Copie o arquivo `.env.example` para `.env`:

```bash
cp .env.example .env
```

Configure as variáveis de ambiente no arquivo `.env`:

```env
APP_NAME="Sistema de Investimentos"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=clubeDoValor
DB_USERNAME=root
DB_PASSWORD=

REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

SESSION_DRIVER=redis
CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
```

### 4. Gere a chave da aplicação

```bash
php artisan key:generate
```

### 5. Execute as migrations e seeders

```bash
php artisan migrate
php artisan db:seed --class=AssetSeeder
```

## 🛠️ Desenvolvimento

### Iniciar servidor de desenvolvimento

```bash
composer run dev
```

Este comando inicia:
- Servidor PHP (Laravel)
- Queue worker
- Vite dev server (hot reload)

### Comandos úteis

```bash
# Executar testes
composer test

# Executar apenas lint
composer lint

# Build de produção
npm run build

# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📁 Estrutura do Projeto

```
app/
├── Http/
│   ├── Controllers/     # Controllers da aplicação
│   ├── Middleware/      # Middleware customizado
│   └── Requests/        # Form Requests (validação)
├── Models/              # Modelos Eloquent
├── Repositories/        # Repositórios (camada de dados)
├── Services/            # Services (lógica de negócio)
└── Providers/          # Service Providers

resources/
├── js/
│   ├── components/      # Componentes Vue reutilizáveis
│   ├── layouts/         # Layouts da aplicação
│   └── pages/           # Páginas Inertia
└── css/                 # Estilos CSS

tests/
├── Feature/             # Testes de integração
└── Unit/                # Testes unitários
```

## 🗄️ Estrutura do Banco de Dados

### Entidades Principais

- **User (Consultor):** Usuários do sistema que gerenciam clientes
- **Client (Cliente):** Clientes gerenciados pelos consultores
- **Asset (Ativos):** Ativos financeiros disponíveis para investimento
- **Investment (Aportes):** Registro de investimentos realizados pelos clientes

### Relacionamentos

- Um **User** pode ter vários **Clients**
- Um **Client** pode ter vários **Investments**
- Um **Asset** pode estar em vários **Investments**
- Um **Investment** pertence a um **Client** e um **Asset**

## 🧪 Testes

### Executar todos os testes

```bash
composer test
```

### Executar apenas testes PHP

```bash
./vendor/bin/phpunit
```

### Executar apenas lint

```bash
composer lint
npm run lint
```

## 🏗️ Arquitetura

O projeto segue o padrão **Repository + Service**, separando responsabilidades:

- **Controllers:** Recebem requisições e retornam respostas
- **Services:** Contêm a lógica de negócio
- **Repositories:** Abstraem o acesso aos dados
- **Models:** Representam as entidades do banco de dados

### Validações

- **Form Requests:** Validação de entrada (ex: `min:1` para valores de investimento)
- **Services:** Validações de negócio adicionais

## 📊 Funcionalidades

### Gestão de Clientes
- Criar, editar e excluir clientes
- Validação de email único por usuário
- Filtros e listagem

### Gestão de Investimentos
- Criar, editar e excluir aportes
- Validação de valor mínimo (R$ 1,00)
- Validação de data (não pode ser futura)
- Estatísticas (total do mês, top ativo com valor total alocado)
- Filtros por cliente

### Dashboard
- Visualização de investimentos
- Estatísticas em tempo real
- Filtros dinâmicos

## 🔐 Autenticação

O sistema utiliza Laravel Breeze com autenticação via sessões. As rotas protegidas requerem autenticação e verificação de email.

## 🚀 Deploy

### Build para produção

```bash
# Instalar dependências sem dev
composer install --no-dev --optimize-autoloader --no-interaction

# Buildar assets
npm run build

# Otimizar Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📝 CI/CD

O projeto possui workflows do GitHub Actions para:

- **Lint:** Verificação de código style (PHP e JavaScript)
- **Tests:** Execução de testes unitários e de integração
- **Browser Tests:** Testes E2E com Playwright

## 🔄 Melhorias Futuras

### Redis para Sessões

Para melhorar a performance e escalabilidade da aplicação, recomenda-se migrar o armazenamento de sessões do banco de dados (MySQL) para Redis. Esta estratégia oferece:

- **Melhor Performance:** Redis é muito mais rápido que consultas ao banco de dados
- **Redução de Carga:** Diminui a carga no MySQL, liberando recursos para operações críticas
- **Escalabilidade:** Permite compartilhar sessões entre múltiplos servidores
- **TTL Automático:** Expiração automática de sessões sem necessidade de limpeza manual

Para implementar, basta configurar no `.env`:
```env
SESSION_DRIVER=redis
CACHE_DRIVER=redis
```

## 📄 Licença

Este projeto está sob a licença MIT.
