# Explicação: Mock do Vite nos Testes

## 🎯 Problema Real

Quando você roda testes que renderizam views (como `test_login_screen_can_be_rendered`), o Laravel precisa processar a view `app.blade.php` que contém:

```blade
@vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
```

**O problema:** O Vite precisa do arquivo `public/build/manifest.json` que só existe após compilar os assets com `npm run build` ou `npm run dev`.

**Nos testes:** Não queremos compilar assets, só queremos testar se a página renderiza corretamente!

## 🔧 Solução: Mock

Um **mock** é uma "simulação" de um objeto. Em vez de usar o Vite real, criamos uma versão falsa que:

- ✅ Não precisa do manifest.json
- ✅ Não tenta carregar assets reais
- ✅ Retorna valores vazios/inofensivos
- ✅ Permite que os testes rodem sem erros

## 📋 O que cada método faz

### 1. `__invoke()` - O mais importante!
```php
Vite::shouldReceive('__invoke')->andReturn('');
```
**Quando é chamado:** Toda vez que você usa `@vite()` na Blade
**O que retorna:** String vazia (não gera HTML de scripts/styles)
**Por quê:** Nos testes não precisamos dos assets reais

### 2. `preloadedAssets()`
```php
Vite::shouldReceive('preloadedAssets')->andReturn([]);
```
**Quando é chamado:** Para pré-carregar assets importantes
**O que retorna:** Array vazio (nenhum asset para pré-carregar)
**Por quê:** Não há assets para pré-carregar nos testes

### 3. Métodos de configuração (retornam `self`)
```php
Vite::shouldReceive('useCspNonce')->andReturnSelf();
Vite::shouldReceive('useIntegrityKey')->andReturnSelf();
Vite::shouldReceive('useScriptTagAttributes')->andReturnSelf();
Vite::shouldReceive('useStyleTagAttributes')->andReturnSelf();
```
**Quando são chamados:** Para configurar segurança e atributos
**O que retornam:** O próprio objeto (`$this`) para permitir encadeamento
**Por quê:** Permite código como `Vite::useCspNonce('abc')->useIntegrityKey('xyz')`

## 🎬 Fluxo nos Testes

```
1. Teste chama: $this->get('/login')
   ↓
2. Laravel renderiza: app.blade.php
   ↓
3. Encontra: @vite(['resources/js/app.js'])
   ↓
4. Laravel chama: Vite::__invoke(['resources/js/app.js'])
   ↓
5. Mock intercepta: Retorna '' (string vazia)
   ↓
6. View renderiza sem erros ✅
   ↓
7. Teste verifica: assertStatus(200) ✅
```

## 💡 Analogia

Imagine que você está testando um carro, mas não quer ligar o motor de verdade:

- **Sem mock:** Tenta ligar o motor real → Precisa de gasolina, bateria, etc. → ❌ Falha
- **Com mock:** Simula que o motor ligou → Não precisa de nada → ✅ Funciona

O mock do Vite funciona assim: simula que os assets foram carregados sem realmente precisar deles!

## ✅ Resultado

Com o mock, seus testes:
- ✅ Rodam mais rápido (não precisam compilar assets)
- ✅ Não dependem de arquivos externos
- ✅ Focam no que realmente importa: testar a lógica da aplicação
- ✅ Funcionam mesmo sem rodar `npm run build` antes
