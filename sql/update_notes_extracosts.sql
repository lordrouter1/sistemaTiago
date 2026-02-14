-- ============================================================
-- Migration: Split notes + Extra costs + Price table on order
-- ============================================================

-- 1. Rename existing 'notes' to 'internal_notes' and add 'quote_notes'
ALTER TABLE orders 
    CHANGE COLUMN notes internal_notes TEXT DEFAULT NULL,
    ADD COLUMN quote_notes TEXT DEFAULT NULL AFTER internal_notes;

-- 2. Create order_extra_costs table
CREATE TABLE IF NOT EXISTS order_extra_costs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    description VARCHAR(255) NOT NULL,
    amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Add price_table_id to orders (for override selection in quote)
ALTER TABLE orders 
    ADD COLUMN price_table_id INT DEFAULT NULL AFTER customer_id;
