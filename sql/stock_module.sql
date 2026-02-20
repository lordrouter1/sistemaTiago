-- ═══════════════════════════════════════════════════════════════
-- MÓDULO DE CONTROLE DE ESTOQUE
-- Tabelas: warehouses, stock_items, stock_movements
-- ═══════════════════════════════════════════════════════════════

-- 1) Armazéns / Locais de Estoque
CREATE TABLE IF NOT EXISTS warehouses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    address VARCHAR(500) DEFAULT NULL,
    city VARCHAR(100) DEFAULT NULL,
    state VARCHAR(2) DEFAULT NULL,
    zip_code VARCHAR(10) DEFAULT NULL,
    phone VARCHAR(20) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Itens de Estoque (produto + variação por armazém)
--    Um registro por (warehouse, product, combination)
CREATE TABLE IF NOT EXISTS stock_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    warehouse_id INT NOT NULL,
    product_id INT NOT NULL,
    combination_id INT DEFAULT NULL COMMENT 'NULL = produto sem variação',
    quantity DECIMAL(12,2) DEFAULT 0,
    min_quantity DECIMAL(12,2) DEFAULT 0 COMMENT 'Estoque mínimo para alerta',
    location_code VARCHAR(50) DEFAULT NULL COMMENT 'Localização física (ex: A1-P3)',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_warehouse_product_combo (warehouse_id, product_id, combination_id),
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (combination_id) REFERENCES product_grade_combinations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Movimentações de Estoque (entradas e saídas)
CREATE TABLE IF NOT EXISTS stock_movements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    stock_item_id INT NOT NULL,
    warehouse_id INT NOT NULL,
    product_id INT NOT NULL,
    combination_id INT DEFAULT NULL,
    type ENUM('entrada','saida','ajuste','transferencia') NOT NULL DEFAULT 'entrada',
    quantity DECIMAL(12,2) NOT NULL,
    quantity_before DECIMAL(12,2) DEFAULT 0,
    quantity_after DECIMAL(12,2) DEFAULT 0,
    reason VARCHAR(255) DEFAULT NULL COMMENT 'Motivo/observação da movimentação',
    reference_type VARCHAR(50) DEFAULT NULL COMMENT 'order, manual, adjustment, transfer',
    reference_id INT DEFAULT NULL COMMENT 'ID do pedido ou outra referência',
    destination_warehouse_id INT DEFAULT NULL COMMENT 'Para transferências entre armazéns',
    user_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (stock_item_id) REFERENCES stock_items(id) ON DELETE CASCADE,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_stock_mov_product (product_id),
    INDEX idx_stock_mov_warehouse (warehouse_id),
    INDEX idx_stock_mov_created (created_at),
    INDEX idx_stock_mov_type (type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir um armazém padrão
INSERT INTO warehouses (name, address, notes) VALUES 
('Estoque Principal', 'Endereço da sede', 'Armazém principal da empresa');
