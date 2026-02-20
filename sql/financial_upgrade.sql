-- ═══════════════════════════════════════════════════════════
-- Migração: Campos financeiros avançados e dados fiscais
-- Data: 2026-02-20
-- ═══════════════════════════════════════════════════════════

-- 1. Adicionar campo de entrada (down payment)
ALTER TABLE orders ADD COLUMN IF NOT EXISTS down_payment DECIMAL(10,2) DEFAULT 0.00 AFTER discount;

-- 2. Adicionar campos de parcelamento (se não existirem)
ALTER TABLE orders ADD COLUMN IF NOT EXISTS installments INT DEFAULT NULL AFTER down_payment;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS installment_value DECIMAL(10,2) DEFAULT NULL AFTER installments;

-- 3. Adicionar campos fiscais (Nota Fiscal)
ALTER TABLE orders ADD COLUMN IF NOT EXISTS nf_number VARCHAR(50) NULL AFTER tracking_code;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS nf_series VARCHAR(10) NULL AFTER nf_number;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS nf_status ENUM('','emitida','enviada','cancelada') DEFAULT '' AFTER nf_series;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS nf_access_key VARCHAR(100) NULL AFTER nf_status;
ALTER TABLE orders ADD COLUMN IF NOT EXISTS nf_notes TEXT NULL AFTER nf_access_key;

-- 4. Atualizar registros existentes
UPDATE orders SET down_payment = 0 WHERE down_payment IS NULL;
UPDATE orders SET nf_status = '' WHERE nf_status IS NULL;
