# Regras e Estrutura do Projeto - Sistema de Gestão para Gráfica

## Visão Geral
Este projeto é uma plataforma online para gráfica e produtos personalizados, focando inicialmente no Sistema de Gestão (ERP/CRM básico).

## Tecnologias e Versões
- **Linguagem Backend:** PHP (Versão 7.4 ou 8.x)
- **Frontend:** HTML5, CSS3, JavaScript
- **Framework CSS:** Bootstrap 5
- **Biblioteca JS:** jQuery (última versão estável)
- **Banco de Dados:** MySQL/MariaDB
- **Arquitetura:** MVC (Model-View-Controller)

## Estrutura de Pastas
O projeto segue a seguinte organização de diretórios:

```
/sistemaTiago
|-- /app
|   |-- /config       # Arquivos de configuração (Banco de dados, Globais)
|   |-- /controllers  # Controladores da aplicação (Lógica de negócio)
|   |-- /models       # Modelos de interação com o banco de dados
|   |-- /views        # Arquivos de visualização (HTML/PHP misto)
|       |-- /layout   # Cabeçalho, Rodapé, Menu lateral
|-- /assets
|   |-- /css          # Estilos customizados
|   |-- /js           # Scripts customizados
|   |-- /img          # Imagens do sistema
|-- /sql              # Scripts SQL para criação e migração do banco
|-- index.php         # Ponto de entrada da aplicação (Router básico)
```

## Padrões de Código (Guidelines)

### PHP & MVC
- **Models:** Devem conter apenas lógica de acesso a dados e regras de negócio puras. Devem extender uma classe `Database` base.
- **Controllers:** Devem receber as requisições, instanciar models e retornar views. Evitar HTML dentro de controllers.
- **Views:** Devem conter HTML e o mínimo de PHP possível (apenas para exibição de dados: `<?= $variavel ?>`).

### Frontend
- Utilizar classes do **Bootstrap 5** para layout e responsividade.
- Arquivos CSS e JS customizados devem ficar separados em `assets/`.
- **jQuery** deve ser utilizado para manipulação de DOM e requisições AJAX.

### Banco de Dados
- Tabelas devem usar nomes no singular ou plural (definir padrão: sugerido **snake_case** e plural, ex: `users`, `products`, `orders`).
- Chaves primárias devem ser `id` (AUTO_INCREMENT).

## Fluxo de Desenvolvimento
Ao realizar modificações:
1. Verifique se a alteração requer mudança no banco de dados (atualizar `/sql`).
2. Mantenha a separação MVC.
3. Garanta que o layout seja responsivo.

## Como Adicionar Novas Páginas (Workflow)
Para adicionar uma nova funcionalidade completa (ex: "Fornecedores"), siga esta ordem rigorosa:

1. **Banco de Dados:** Crie a tabela necessária no banco (e salve o script em `/sql`).
2. **Model:** Crie o arquivo (ex: `app/models/Supplier.php`).
   - Deve estender nenhuma classe (recebe `$db` no construtor).
   - Deve conter métodos CRUD: `create()`, `readAll()`, `readOne()`, `update()`, `delete()`.
3. **Controller:** Crie o controller (ex: `app/controllers/SupplierController.php`).
   - Deve ter métodos públicos mapeados para ações: `index()` (listagem), `create()` (exibir form), `store()` (processar form).
   - Deve fazer a checagem de permissão no início de cada método.
4. **View:** Crie a pasta e arquivos (ex: `app/views/suppliers/index.php`).
   - Use `header.php` e `footer.php` para manter o layout.
5. **Rotas (Router):** Edite o arquivo `index.php` na raiz.
   - Adicione um novo `case 'nome_pagina':` no switch principal.
   - Instancie o controller e chame o método baseado na `action`.
6. **Permissões:**
   - Adicione a nova página ao array `$pages` no arquivo `app/views/users/groups.php` para que ela apareça na gestão de grupos.
   - Adicione o link no menu em `app/views/layout/header.php` (com verificação de permissão se necessário).

## Onde colocar cada código? (Responsabilidades MVC)

### 1. Models (`app/models/`)
**Responsabilidade:** Acesso a dados e Regras de Negócio.
- **Deve conter:** Queries SQL (`INSERT`, `SELECT`, etc), validação de dados antes de salvar (ex: checar duplicidade de email).
- **NÃO pode conter:** HTML, `echo`, `print`, acesso direto a `$_POST` ou `$_GET`.

### 2. Controllers (`app/controllers/`)
**Responsabilidade:** Recepcionista e Gerente.
- **Deve conter:** Captura de dados do formulário (`$_POST`), verificação de login (`checkAdmin`), instanciação de Models, decisão de qual View mostrar, Redirecionamentos (`header('Location: ...')`), mensagens de erro/sucesso.
- **NÃO pode conter:** Queries SQL diretas, HTML complexo.

### 3. Views (`app/views/`)
**Responsabilidade:** Interface com o Usuário.
- **Deve conter:** Estrutura HTML, formulários, loops (`foreach`) para exibir listas de dados vindas do controller.
- **NÃO pode conter:** Lógica de banco de dados, alterações de registro, lógica complexa de PHP. A View apenas **mostra** o que o Controller entregou.

## Rotas do Sistema (Router - index.php)
O roteamento é baseado nos parâmetros `page` e `action` via GET.

| Page       | Descrição                         | Requer Login | Permissão  |
|------------|-----------------------------------|--------------|------------|
| `home`     | Página inicial (landing)          | Sim          | Livre      |
| `login`    | Login/Logout                      | Não          | —          |
| `dashboard`| Painel de controle                | Sim          | Livre      |
| `profile`  | Perfil do usuário logado          | Sim          | Livre      |
| `customers`| CRUD de Clientes                  | Sim          | Por grupo  |
| `products` | CRUD de Produtos                  | Sim          | Por grupo  |
| `orders`   | CRUD de Pedidos                   | Sim          | Por grupo  |
| `users`    | Gestão de Usuários/Grupos (Admin) | Sim          | Admin only |

### Padrão de Actions por módulo
- `index` → Listagem (padrão)
- `create` → Exibir formulário de criação
- `store` → Processar formulário de criação (POST)
- `edit` → Exibir formulário de edição
- `update` → Processar formulário de edição (POST)
- `delete` → Excluir registro

## Bibliotecas e Frameworks Frontend
- **Bootstrap 5** — Layout e componentes UI
- **jQuery 3.7** — Manipulação DOM e AJAX
- **Font Awesome 6** — Ícones
- **SweetAlert2** — Alertas e confirmações visuais (substituir `confirm()` e `alert()`)
- **jQuery Mask** — Máscaras de input (CPF, telefone, CEP)

### Padrão para Feedback Visual (SweetAlert2)
- Após ações de CRUD, redirecionar com `?status=success` na URL.
- Na view de listagem, verificar `$_GET['status']` e disparar `Swal.fire()`.
- Para exclusões, usar `Swal.fire()` com confirmação antes de prosseguir.
- Nunca usar `confirm()` ou `alert()` nativo do JavaScript.

### Menu Superior (header.php)
- O **nome do usuário** sempre redireciona para o **Perfil** (`?page=profile`).
- O ícone de **engrenagem (⚙️)** redireciona para a **Gestão de Usuários** (`?page=users`) e **só aparece para admin**.
- O botão de **Sair** faz logout (`?page=login&action=logout`).
- O menu é fixo no topo (`sticky-top`) e não muda de tamanho ao selecionar itens.

### Padrão de Formulários (Create/Edit)
- Os formulários de **criação** e **edição** de cada módulo devem ser **visualmente idênticos**.
- Ambos devem usar a mesma estrutura de fieldsets, mesmos campos, mesmos labels e mesmo layout.
- A diferença é que no edit os campos vêm pré-preenchidos com `value="<?= $model['campo'] ?>"` e o form action aponta para `action=update` em vez de `action=store`.
- Formulários de edição incluem um `<input type="hidden" name="id">` com o ID do registro.
