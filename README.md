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

## Melhorias Futuras

### Redis para Sessões

Para melhorar a performance e escalabilidade da aplicação, recomenda-se migrar o armazenamento de sessões do banco de dados (MySQL) para Redis. Esta estratégia oferece:

- **Melhor Performance:** Redis é muito mais rápido que consultas ao banco de dados
- **Redução de Carga:** Diminui a carga no MySQL, liberando recursos para operações críticas
- **Escalabilidade:** Permite compartilhar sessões entre múltiplos servidores
- **TTL Automático:** Expiração automática de sessões sem necessidade de limpeza manual

Para implementar, basta configurar no `.env`:
```
SESSION_DRIVER=redis
CACHE_STORE=redis
```
