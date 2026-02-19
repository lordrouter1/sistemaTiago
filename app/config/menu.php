<?php
/**
 * Registro centralizado de todas as páginas/módulos do sistema.
 * 
 * Este array é a ÚNICA fonte de verdade para:
 *   - Itens do menu principal (header.php)
 *   - Permissões de grupos (groups.php)
 * 
 * Ao adicionar uma nova página ao sistema, basta incluí-la aqui
 * e ela aparecerá automaticamente no menu e nas permissões de grupo.
 * 
 * Estrutura de cada entrada:
 *   'page_key' => [
 *       'label'      => 'Nome exibido',
 *       'icon'       => 'Classe Font Awesome',
 *       'menu'       => true/false  — exibe no menu principal
 *       'permission' => true/false  — aparece na lista de permissões de grupo
 *   ]
 * 
 * Páginas com 'permission' => false são acessíveis por todos (home, dashboard, profile, pipeline).
 * Páginas com 'menu' => false não aparecem na navbar (ex: profile, users — ficam no menu direito).
 */

return [
    'home' => [
        'label'      => 'Início',
        'icon'       => 'fas fa-home',
        'menu'       => true,
        'permission' => false,
    ],
    'dashboard' => [
        'label'      => 'Dashboard',
        'icon'       => 'fas fa-tachometer-alt',
        'menu'       => true,
        'permission' => true,
    ],
    'customers' => [
        'label'      => 'Clientes',
        'icon'       => 'fas fa-users',
        'menu'       => true,
        'permission' => true,
    ],
    'products' => [
        'label'      => 'Produtos',
        'icon'       => 'fas fa-box-open',
        'menu'       => true,
        'permission' => true,
    ],
    'price_tables' => [
        'label'      => 'Tabelas de Preço',
        'icon'       => 'fas fa-tags',
        'menu'       => true,
        'permission' => true,
    ],
    'orders' => [
        'label'      => 'Pedidos',
        'icon'       => 'fas fa-shopping-cart',
        'menu'       => true,
        'permission' => true,
    ],
    'pipeline' => [
        'label'      => 'Produção',
        'icon'       => 'fas fa-stream',
        'menu'       => true,
        'permission' => true,
    ],
    'settings' => [
        'label'      => 'Configurações',
        'icon'       => 'fas fa-building',
        'menu'       => false,   // Fica no menu direito (engrenagem)
        'permission' => true,
    ],
    'users' => [
        'label'      => 'Gestão de Usuários',
        'icon'       => 'fas fa-cog',
        'menu'       => false,   // Fica no menu direito (engrenagem), não no principal
        'permission' => true,
    ],
    'profile' => [
        'label'      => 'Meu Perfil',
        'icon'       => 'fas fa-user-circle',
        'menu'       => false,   // Fica no menu direito
        'permission' => false,
    ],
];
