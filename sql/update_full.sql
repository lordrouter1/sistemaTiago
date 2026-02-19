-- ============================================================
-- ATUALIZAÇÃO COMPLETA DO BANCO DE DADOS
-- sistema_grafica — Gerado em 18/02/2026
-- 
-- Este script sincroniza o banco de dados com todos os models
-- e funcionalidades atuais do sistema.
-- Seguro para executar múltiplas vezes (usa IF NOT EXISTS / IF).
-- ============================================================

USE sistema_grafica;

-- ─────────────────────────────────────────────────────
-- 1. Tabela: price_tables (Tabelas de Preço)
--    Referenciada por: PriceTable.php
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS price_tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT DEFAULT NULL,
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir tabela de preço padrão se não existir nenhuma
INSERT INTO price_tables (name, description, is_default)
SELECT 'Tabela Padrão', 'Tabela de preços padrão do sistema', 1
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM price_tables WHERE is_default = 1);

-- ─────────────────────────────────────────────────────
-- 2. Tabela: price_table_items (Itens das Tabelas de Preço)
--    Referenciada por: PriceTable.php
-- ─────────────────────────────────────────────────────
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
-- 3. Tabela: company_settings (Configurações da Empresa)
--    Referenciada por: CompanySettings.php
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS company_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) NOT NULL UNIQUE,
    setting_value TEXT DEFAULT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- 4. Tabela: order_extra_costs (Custos Extras dos Pedidos)
--    Referenciada por: Order.php
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS order_extra_costs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- 5. Coluna: customers.price_table_id
--    FK para price_tables — Define tabela de preço do cliente
-- ─────────────────────────────────────────────────────
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_grafica' AND TABLE_NAME = 'customers' AND COLUMN_NAME = 'price_table_id');

SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE customers ADD COLUMN price_table_id INT DEFAULT NULL AFTER photo, ADD FOREIGN KEY (price_table_id) REFERENCES price_tables(id) ON DELETE SET NULL',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ─────────────────────────────────────────────────────
-- 6. Coluna: orders — Renomear notes → internal_notes
--    E adicionar quote_notes
-- ─────────────────────────────────────────────────────

-- 6a. Renomear 'notes' → 'internal_notes' (se 'notes' ainda existir)
SET @has_notes = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_grafica' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'notes');
SET @has_internal = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_grafica' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'internal_notes');

SET @sql = IF(@has_notes > 0 AND @has_internal = 0,
    'ALTER TABLE orders CHANGE COLUMN notes internal_notes TEXT DEFAULT NULL',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6b. Se nem 'notes' nem 'internal_notes' existem, criar internal_notes
SET @has_internal2 = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_grafica' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'internal_notes');

SET @sql = IF(@has_internal2 = 0,
    'ALTER TABLE orders ADD COLUMN internal_notes TEXT DEFAULT NULL AFTER priority',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 6c. Adicionar quote_notes
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_grafica' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'quote_notes');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN quote_notes TEXT DEFAULT NULL AFTER internal_notes',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ─────────────────────────────────────────────────────
-- 7. Coluna: orders.scheduled_date
--    Usada pelo pipeline para agendar contatos
-- ─────────────────────────────────────────────────────
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_grafica' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'scheduled_date');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN scheduled_date DATE DEFAULT NULL AFTER quote_notes',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ─────────────────────────────────────────────────────
-- 8. Coluna: orders.price_table_id
--    Permite override de tabela de preço por pedido
-- ─────────────────────────────────────────────────────
SET @col_exists = (SELECT COUNT(*) FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'sistema_grafica' AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'price_table_id');

SET @sql = IF(@col_exists = 0,
    'ALTER TABLE orders ADD COLUMN price_table_id INT DEFAULT NULL AFTER customer_id',
    'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ─────────────────────────────────────────────────────
-- 9. Tabela: production_sectors (Setores de Produção)
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

-- ─────────────────────────────────────────────────────
-- 10. Tabela: product_sectors (Vínculo Produto → Setores)
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_sectors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    sector_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (sector_id) REFERENCES production_sectors(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_sector (product_id, sector_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- 11. Tabela: catalog_links (Links de Catálogo para Clientes)
--     Permite gerar links públicos de catálogo vinculados a pedidos
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS catalog_links (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE,
    show_prices TINYINT(1) DEFAULT 1,
    is_active TINYINT(1) DEFAULT 1,
    expires_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- 12. Verificação final — Listar todas as tabelas
-- ─────────────────────────────────────────────────────
SELECT '✅ Atualização concluída com sucesso!' AS resultado;
SHOW TABLES;
