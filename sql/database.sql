-- ============================================================
-- BANCO DE DADOS COMPLETO — sistema_grafica
-- Atualizado em: 18/02/2026
-- 
-- Inclui todas as tabelas necessárias para o sistema funcionar.
-- Seguro para executar em banco novo (usa IF NOT EXISTS).
-- ============================================================

CREATE DATABASE IF NOT EXISTS sistema_grafica;
USE sistema_grafica;

-- ─────────────────────────────────────────────────────
-- MÓDULO: USUÁRIOS E PERMISSÕES
-- ─────────────────────────────────────────────────────

-- Grupos de Usuários (deve ser criada ANTES de users)
CREATE TABLE IF NOT EXISTS user_groups (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuários (Administradores/Funcionários)
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'funcionario') DEFAULT 'funcionario',
    group_id INT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (group_id) REFERENCES user_groups(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Permissões dos Grupos (pages que cada grupo pode acessar)
CREATE TABLE IF NOT EXISTS group_permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    group_id INT NOT NULL,
    page_name VARCHAR(50) NOT NULL,
    FOREIGN KEY (group_id) REFERENCES user_groups(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- MÓDULO: CONFIGURAÇÕES DA EMPRESA
-- ─────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS company_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- MÓDULO: TABELAS DE PREÇO
-- ─────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS price_tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir tabela de preço padrão
INSERT IGNORE INTO price_tables (id, name, description, is_default) 
VALUES (1, 'Tabela Padrão', 'Tabela de preços padrão do sistema', 1);

-- ─────────────────────────────────────────────────────
-- MÓDULO: CLIENTES
-- ─────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS customers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20),
    document VARCHAR(20),
    address TEXT,
    photo VARCHAR(255),
    price_table_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (price_table_id) REFERENCES price_tables(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- MÓDULO: PRODUTOS
-- ─────────────────────────────────────────────────────

-- Categorias
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Subcategorias
CREATE TABLE IF NOT EXISTS subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category_id INT NOT NULL,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Produtos/Serviços
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    category_id INT NULL,
    subcategory_id INT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock_quantity INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Imagens do Produto (Galeria)
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Itens das Tabelas de Preço (preço customizado por tabela/produto)
CREATE TABLE IF NOT EXISTS price_table_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    price_table_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (price_table_id) REFERENCES price_tables(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_table_product (price_table_id, product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- MÓDULO: PEDIDOS
-- ─────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    price_table_id INT DEFAULT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('orcamento', 'pendente', 'Pendente', 'aprovado', 'em_producao', 'concluido', 'cancelado') DEFAULT 'orcamento',
    pipeline_stage ENUM('contato','orcamento','venda','producao','preparacao','envio','financeiro','concluido') DEFAULT 'contato',
    pipeline_entered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    deadline DATE NULL,
    priority ENUM('baixa','normal','alta','urgente') DEFAULT 'normal',
    internal_notes TEXT DEFAULT NULL,
    quote_notes TEXT DEFAULT NULL,
    scheduled_date DATE DEFAULT NULL,
    assigned_to INT NULL,
    payment_status ENUM('pendente','parcial','pago') DEFAULT 'pendente',
    payment_method VARCHAR(50) NULL,
    discount DECIMAL(10,2) DEFAULT 0.00,
    shipping_type ENUM('retirada','entrega','correios') DEFAULT 'retirada',
    shipping_address TEXT NULL,
    tracking_code VARCHAR(100) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Itens do Pedido
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    unit_price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Custos Extras dos Pedidos
CREATE TABLE IF NOT EXISTS order_extra_costs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- MÓDULO: PIPELINE DE PRODUÇÃO
-- ─────────────────────────────────────────────────────

-- Histórico de movimentação do pipeline
CREATE TABLE IF NOT EXISTS pipeline_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    from_stage VARCHAR(30) NULL,
    to_stage VARCHAR(30) NOT NULL,
    changed_by INT NULL,
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Metas por etapa do pipeline (tempo máximo em horas)
CREATE TABLE IF NOT EXISTS pipeline_stage_goals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stage VARCHAR(30) NOT NULL UNIQUE,
    stage_label VARCHAR(50) NOT NULL,
    max_hours INT NOT NULL DEFAULT 24,
    stage_order INT NOT NULL DEFAULT 0,
    color VARCHAR(20) DEFAULT '#3498db',
    icon VARCHAR(50) DEFAULT 'fas fa-circle',
    is_active TINYINT(1) DEFAULT 1,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Metas padrão do pipeline
INSERT IGNORE INTO pipeline_stage_goals (stage, stage_label, max_hours, stage_order, color, icon) VALUES
('contato',    'Contato',       24,  1, '#9b59b6', 'fas fa-phone'),
('orcamento',  'Orçamento',     48,  2, '#3498db', 'fas fa-file-invoice-dollar'),
('venda',      'Venda',         24,  3, '#2ecc71', 'fas fa-handshake'),
('producao',   'Produção',      72,  4, '#e67e22', 'fas fa-industry'),
('preparacao', 'Preparação',    24,  5, '#1abc9c', 'fas fa-boxes-packing'),
('envio',      'Envio/Entrega', 48,  6, '#e74c3c', 'fas fa-truck'),
('financeiro', 'Financeiro',    48,  7, '#f39c12', 'fas fa-coins'),
('concluido',  'Concluído',      0,  8, '#27ae60', 'fas fa-check-double');

-- ─────────────────────────────────────────────────────
-- MÓDULO: SETORES DE PRODUÇÃO
-- ─────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS production_sectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-cogs',
    color VARCHAR(20) DEFAULT '#6c757d',
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Setores vinculados a produtos (quais setores o produto passa)
CREATE TABLE IF NOT EXISTS product_sectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    sector_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (sector_id) REFERENCES production_sectors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_sector (product_id, sector_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Setores vinculados a categorias (setores padrão da categoria)
CREATE TABLE IF NOT EXISTS category_sectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    sector_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (sector_id) REFERENCES production_sectors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_cat_sector (category_id, sector_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Setores vinculados a subcategorias (setores padrão da subcategoria)
CREATE TABLE IF NOT EXISTS subcategory_sectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subcategory_id INT NOT NULL,
    sector_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE,
    FOREIGN KEY (sector_id) REFERENCES production_sectors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_sub_sector (subcategory_id, sector_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- MÓDULO: LOGS DO SISTEMA
-- ─────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS system_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    action VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
