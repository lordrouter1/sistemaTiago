-- ============================================================
-- MIGRAÇÃO: Sistema de Grades para Categorias e Subcategorias
-- 
-- Permite que categorias e subcategorias definam grades padrão.
-- Ao criar um produto, as grades são herdadas da subcategoria 
-- (prioridade) ou da categoria, se existirem.
-- Suporta inativação de combinações em todos os níveis.
-- ============================================================

USE sistema_grafica;

-- ─────────────────────────────────────────────────────
-- GRADES vinculadas a uma CATEGORIA
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS category_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    grade_type_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    FOREIGN KEY (grade_type_id) REFERENCES product_grade_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category_grade (category_id, grade_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Valores de cada grade de categoria
CREATE TABLE IF NOT EXISTS category_grade_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_grade_id INT NOT NULL,
    value VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_grade_id) REFERENCES category_grades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Combinações de grades de categoria (para controle de inativação)
CREATE TABLE IF NOT EXISTS category_grade_combinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    combination_key VARCHAR(255) NOT NULL,
    combination_label VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_category_combination (category_id, combination_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────
-- GRADES vinculadas a uma SUBCATEGORIA
-- ─────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS subcategory_grades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subcategory_id INT NOT NULL,
    grade_type_id INT NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE,
    FOREIGN KEY (grade_type_id) REFERENCES product_grade_types(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subcategory_grade (subcategory_id, grade_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Valores de cada grade de subcategoria
CREATE TABLE IF NOT EXISTS subcategory_grade_values (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subcategory_grade_id INT NOT NULL,
    value VARCHAR(100) NOT NULL,
    sort_order INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subcategory_grade_id) REFERENCES subcategory_grades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Combinações de grades de subcategoria (para controle de inativação)
CREATE TABLE IF NOT EXISTS subcategory_grade_combinations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subcategory_id INT NOT NULL,
    combination_key VARCHAR(255) NOT NULL,
    combination_label VARCHAR(500) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subcategory_id) REFERENCES subcategories(id) ON DELETE CASCADE,
    UNIQUE KEY unique_subcategory_combination (subcategory_id, combination_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
