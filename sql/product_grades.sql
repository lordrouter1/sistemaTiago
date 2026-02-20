-- ============================================================
-- MIGRAÇÃO: Sistema de Grades (Variações) de Produtos
-- 
-- Permite que cada produto tenha múltiplas grades independentes
-- (ex: Tamanho, Cor, Material) com valores configuráveis.
-- Suporta combinações para controle de estoque/preço por variação.
-- ============================================================

USE sistema_grafica;

-- ─────────────────────────────────────────────────────
-- Tipos de Grade disponíveis (templates reutilizáveis)
-- Ex: "Tamanho", "Cor", "Material", "Acabamento"
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_grade_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    description VARCHAR(255) DEFAULT NULL,
    icon VARCHAR(50) DEFAULT 'fas fa-th',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inserir tipos de grade comuns para gráficas
INSERT IGNORE INTO product_grade_types (name, description, icon) VALUES
('Tamanho', 'Variações de tamanho do produto (P, M, G, GG, etc.)', 'fas fa-ruler-combined'),
('Cor', 'Variações de cor do produto', 'fas fa-palette'),
('Material', 'Tipo de material ou papel utilizado', 'fas fa-layer-group'),
('Acabamento', 'Tipo de acabamento (laminação, verniz, etc.)', 'fas fa-magic'),
('Gramatura', 'Gramatura do papel (90g, 150g, 300g, etc.)', 'fas fa-weight-hanging'),
('Formato', 'Formato ou dimensão do produto', 'fas fa-expand-arrows-alt'),
('Quantidade', 'Faixas de quantidade (100un, 500un, 1000un)', 'fas fa-boxes');

-- ─────────────────────────────────────────────────────
-- Grades vinculadas a um produto
-- Cada produto pode ter múltiplas grades (ex: Camiseta tem "Tamanho" E "Cor")
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    grade_type_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (grade_type_id) REFERENCES product_grade_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_grade (product_id, grade_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- Valores de cada grade de um produto
-- Ex: Produto "Camiseta" > Grade "Tamanho" > Valores: "P", "M", "G", "GG"
-- Ex: Produto "Camiseta" > Grade "Cor" > Valores: "Branca", "Preta", "Azul"
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_grade_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_grade_id INT NOT NULL,
    value VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_grade_id) REFERENCES product_grades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- Combinações de grades (estoque e preço por combinação)
-- Ex: Camiseta Tamanho=M + Cor=Branca => estoque=50, preço extra=0
-- Se o produto tiver apenas 1 grade, há 1 combinação por valor.
-- Se tiver 2 grades, é o produto cartesiano dos valores.
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS product_grade_combinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    combination_key VARCHAR(255) NOT NULL COMMENT 'Chave serializada ex: "2:5|3:8" (grade_id:value_id)',
    combination_label VARCHAR(500) DEFAULT NULL COMMENT 'Label legível ex: "M / Branca"',
    sku VARCHAR(100) DEFAULT NULL,
    price_override DECIMAL(10,2) DEFAULT NULL COMMENT 'Preço específico da combinação (NULL = usa preço do produto)',
    stock_quantity INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_combination (product_id, combination_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- Coluna de grade nos itens do pedido (qual combinação foi escolhida)
-- ─────────────────────────────────────────────────────
ALTER TABLE order_items 
    ADD COLUMN grade_combination_id INT DEFAULT NULL AFTER product_id,
    ADD COLUMN grade_description VARCHAR(500) DEFAULT NULL AFTER grade_combination_id;

-- Nota: grade_description armazena texto legível da combinação escolhida
-- para manter histórico mesmo se a grade for alterada depois.
-- Ex: "Tamanho: M | Cor: Branca"

