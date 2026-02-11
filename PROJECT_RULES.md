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
