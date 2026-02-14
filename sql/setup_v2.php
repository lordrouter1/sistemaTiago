<?php
/**
 * Setup V2: Company Settings, Price Tables, Customer price_table_id
 */
require_once __DIR__ . '/../app/config/database.php';
$db = (new Database())->getConnection();

echo "<pre>\n";

// 1. Tabela de configurações da empresa
$db->exec("CREATE TABLE IF NOT EXISTS company_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
echo "✅ company_settings criada\n";

// 2. Inserir dados padrão
$defaults = [
    ['company_name', 'Minha Gráfica'],
    ['company_document', ''],
    ['company_phone', ''],
    ['company_email', ''],
    ['company_website', ''],
    ['company_logo', ''],
    ['company_zipcode', ''],
    ['company_address_type', 'Rua'],
    ['company_address_name', ''],
    ['company_address_number', ''],
    ['company_neighborhood', ''],
    ['company_complement', ''],
    ['company_city', ''],
    ['company_state', ''],
    ['quote_validity_days', '15'],
    ['quote_footer_note', 'Os valores podem sofrer alterações sem aviso prévio após o vencimento.'],
];
$ins = $db->prepare('INSERT IGNORE INTO company_settings (setting_key, setting_value) VALUES (?, ?)');
foreach ($defaults as $d) {
    $ins->execute($d);
}
echo "✅ Configurações padrão inseridas\n";

// 3. Tabela de tabelas de preço
$db->exec("CREATE TABLE IF NOT EXISTS price_tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    is_default TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");
echo "✅ price_tables criada\n";

// 4. Itens da tabela de preço
$db->exec("CREATE TABLE IF NOT EXISTS price_table_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    price_table_id INT NOT NULL,
    product_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    UNIQUE KEY unique_table_product (price_table_id, product_id),
    FOREIGN KEY (price_table_id) REFERENCES price_tables(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)");
echo "✅ price_table_items criada\n";

// 5. Adicionar coluna price_table_id em customers
try {
    $db->exec('ALTER TABLE customers ADD COLUMN price_table_id INT NULL');
    echo "✅ customers.price_table_id adicionada\n";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "ℹ️  customers.price_table_id já existe\n";
    } else {
        echo "⚠️  Erro ao adicionar price_table_id: " . $e->getMessage() . "\n";
    }
}

// Tentar adicionar FK
try {
    $db->exec('ALTER TABLE customers ADD FOREIGN KEY (price_table_id) REFERENCES price_tables(id) ON DELETE SET NULL');
    echo "✅ FK customers->price_tables adicionada\n";
} catch (PDOException $e) {
    echo "ℹ️  FK customers->price_tables já existe ou erro: " . $e->getMessage() . "\n";
}

// 6. Inserir tabela de preço padrão
$check = $db->query("SELECT COUNT(*) FROM price_tables WHERE is_default = 1")->fetchColumn();
if ($check == 0) {
    $db->exec("INSERT INTO price_tables (name, description, is_default) VALUES ('Tabela Padrão', 'Tabela de preço padrão do sistema', 1)");
    echo "✅ Tabela de preço padrão inserida\n";
} else {
    echo "ℹ️  Tabela de preço padrão já existe\n";
}

echo "\n🎉 Setup V2 concluído com sucesso!\n";
echo "</pre>";
