# Sistema de Gestão de Investimentos

Sistema desenvolvido em Laravel, Vue.js e Inertia para gestão de investimentos.

## Tecnologias

- **Backend:** Laravel 12
- **Frontend:** Vue.js 3 + TypeScript + Inertia.js
- **UI:** Tailwind CSS + shadcn-vue
- **Banco de Dados:** MySQL

## Estrutura do Banco de Dados

### Entidades Principais

- **User (Consultor):** Usuários do sistema que gerenciam clientes
- **Client (Cliente):** Clientes gerenciados pelos consultores
- **Asset (Ativos):** Ativos financeiros disponíveis para investimento
- **Investment (Aportes):** Registro de investimentos realizados pelos clientes

## Instalação

1. Clone o repositório
2. Instale as dependências:
   ```bash
   composer install
   npm install
   ```
3. Configure o arquivo `.env`
4. Execute as migrations:
   ```bash
   php artisan migrate
   php artisan db:seed --class=AssetSeeder
   ```

## Desenvolvimento

```bash
# Iniciar servidor de desenvolvimento
composer run dev
```
