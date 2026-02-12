-- =====================================================
-- MÓDULO: LINHA DE PRODUÇÃO (Pipeline)
-- Controla o fluxo completo do pedido na gráfica
-- Instruções: Execute este script após o database.sql
-- =====================================================

USE sistema_grafica;

-- ─────────────────────────────────────────────────────
-- 1. Atualizar ENUM de status na tabela orders
-- ─────────────────────────────────────────────────────
ALTER TABLE orders MODIFY COLUMN status ENUM('orcamento','pendente','Pendente','aprovado','em_producao','concluido','cancelado') DEFAULT 'orcamento';

-- ─────────────────────────────────────────────────────
-- 2. Adicionar colunas do pipeline na tabela orders
-- ─────────────────────────────────────────────────────
ALTER TABLE orders
    ADD COLUMN pipeline_stage ENUM(
        'contato',
        'orcamento',
        'venda',
        'producao',
        'preparacao',
        'envio',
        'financeiro',
        'concluido'
    ) DEFAULT 'contato' AFTER status,
    ADD COLUMN pipeline_entered_at DATETIME DEFAULT CURRENT_TIMESTAMP AFTER pipeline_stage,
    ADD COLUMN deadline DATE NULL AFTER pipeline_entered_at,
    ADD COLUMN priority ENUM('baixa','normal','alta','urgente') DEFAULT 'normal' AFTER deadline,
    ADD COLUMN notes TEXT NULL AFTER priority,
    ADD COLUMN assigned_to INT NULL AFTER notes,
    ADD COLUMN payment_status ENUM('pendente','parcial','pago') DEFAULT 'pendente' AFTER assigned_to,
    ADD COLUMN payment_method VARCHAR(50) NULL AFTER payment_status,
    ADD COLUMN discount DECIMAL(10,2) DEFAULT 0.00 AFTER payment_method,
    ADD COLUMN shipping_type ENUM('retirada','entrega','correios') DEFAULT 'retirada' AFTER discount,
    ADD COLUMN shipping_address TEXT NULL AFTER shipping_type,
    ADD COLUMN tracking_code VARCHAR(100) NULL AFTER shipping_address;

-- ─────────────────────────────────────────────────────
-- 3. Tabela de histórico de movimentação do pipeline
-- ─────────────────────────────────────────────────────
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
);

-- ─────────────────────────────────────────────────────
-- 4. Tabela de metas por etapa (tempo máximo em horas)
-- ─────────────────────────────────────────────────────
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
);

-- ─────────────────────────────────────────────────────
-- 5. Inserir metas padrão (ignora duplicatas)
-- ─────────────────────────────────────────────────────
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
-- 6. Atualizar pedidos existentes sem pipeline_stage
-- ─────────────────────────────────────────────────────
UPDATE orders SET pipeline_stage = 'contato', pipeline_entered_at = created_at WHERE pipeline_stage IS NULL;
UPDATE orders SET priority = 'normal' WHERE priority IS NULL;
UPDATE orders SET payment_status = 'pendente' WHERE payment_status IS NULL;
UPDATE orders SET shipping_type = 'retirada' WHERE shipping_type IS NULL;
