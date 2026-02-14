<?php
/**
 * Script de configuração do banco de dados e criação do usuário admin.
 * Execute via navegador: http://localhost/sistemaTiago/sql/setup.php
 * Apague este arquivo após a execução por segurança.
 */

echo "<pre style='font-family:monospace; font-size:14px; background:#1e1e1e; color:#0f0; padding:20px;'>";
echo "======================================\n";
echo " SETUP - Sistema Gráfica\n";
echo "======================================\n\n";

// 1. Conectar ao MySQL (sem selecionar banco)
try {
    $pdo = new PDO("mysql:host=localhost", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("SET NAMES utf8");
    echo "[OK] Conexão com MySQL estabelecida.\n";
} catch (PDOException $e) {
    die("[ERRO] Falha na conexão: " . $e->getMessage() . "\n");
}

// 2. Criar banco de dados
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS sistema_grafica CHARACTER SET utf8 COLLATE utf8_general_ci");
    echo "[OK] Banco de dados 'sistema_grafica' verificado/criado.\n";
} catch (PDOException $e) {
    die("[ERRO] Criar banco: " . $e->getMessage() . "\n");
}

$pdo->exec("USE sistema_grafica");

// 3. Criar tabelas na ordem correta (respeitando FKs)
$tables = [
    'user_groups' => "CREATE TABLE IF NOT EXISTS user_groups (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(50) NOT NULL UNIQUE,
        description TEXT
    )",
    
    'users' => "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role ENUM('admin', 'funcionario') DEFAULT 'funcionario',
        group_id INT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (group_id) REFERENCES user_groups(id) ON DELETE SET NULL
    )",

    'group_permissions' => "CREATE TABLE IF NOT EXISTS group_permissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        group_id INT NOT NULL,
        page_name VARCHAR(50) NOT NULL,
        FOREIGN KEY (group_id) REFERENCES user_groups(id) ON DELETE CASCADE
    )",

    'customers' => "CREATE TABLE IF NOT EXISTS customers (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100),
        phone VARCHAR(20),
        document VARCHAR(20),
        address TEXT,
        photo VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    'categories' => "CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL UNIQUE
    )",

    'subcategories' => "CREATE TABLE IF NOT EXISTS subcategories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        category_id INT NOT NULL,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )",

    'system_logs' => "CREATE TABLE IF NOT EXISTS system_logs (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NULL,
        action VARCHAR(50) NOT NULL,
        details TEXT,
        ip_address VARCHAR(45),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
    )",

    'products' => "CREATE TABLE IF NOT EXISTS products (
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
    )",

    'orders' => "CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        total_amount DECIMAL(10, 2) NOT NULL,
        status ENUM('orcamento', 'pendente', 'Pendente', 'aprovado', 'em_producao', 'concluido', 'cancelado') DEFAULT 'orcamento',
        pipeline_stage ENUM('contato','orcamento','venda','producao','preparacao','envio','financeiro','concluido') DEFAULT 'contato',
        pipeline_entered_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        deadline DATE NULL,
        priority ENUM('baixa','normal','alta','urgente') DEFAULT 'normal',
        notes TEXT NULL,
        scheduled_date DATE NULL,
        assigned_to INT NULL,
        payment_status ENUM('pendente','parcial','pago') DEFAULT 'pendente',
        payment_method VARCHAR(50) NULL,
        discount DECIMAL(10,2) DEFAULT 0.00,
        shipping_type ENUM('retirada','entrega','correios') DEFAULT 'retirada',
        shipping_address TEXT NULL,
        tracking_code VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(id)
    )",

    'order_items' => "CREATE TABLE IF NOT EXISTS order_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        product_id INT NOT NULL,
        quantity INT NOT NULL,
        unit_price DECIMAL(10, 2) NOT NULL,
        subtotal DECIMAL(10, 2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES orders(id),
        FOREIGN KEY (product_id) REFERENCES products(id)
    )",

    'product_images' => "CREATE TABLE IF NOT EXISTS product_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        is_main TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )",

    'pipeline_history' => "CREATE TABLE IF NOT EXISTS pipeline_history (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        from_stage VARCHAR(30) NULL,
        to_stage VARCHAR(30) NOT NULL,
        changed_by INT NULL,
        notes TEXT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
        FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
    )",

    'pipeline_stage_goals' => "CREATE TABLE IF NOT EXISTS pipeline_stage_goals (
        id INT AUTO_INCREMENT PRIMARY KEY,
        stage VARCHAR(30) NOT NULL UNIQUE,
        stage_label VARCHAR(50) NOT NULL,
        max_hours INT NOT NULL DEFAULT 24,
        stage_order INT NOT NULL DEFAULT 0,
        color VARCHAR(20) DEFAULT '#3498db',
        icon VARCHAR(50) DEFAULT 'fas fa-circle',
        is_active TINYINT(1) DEFAULT 1,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )",
];

$errors = 0;
foreach ($tables as $name => $sql) {
    try {
        $pdo->exec($sql);
        echo "[OK] Tabela '$name' verificada/criada.\n";
    } catch (PDOException $e) {
        echo "<span style='color:red'>[ERRO] Tabela '$name': " . $e->getMessage() . "</span>\n";
        $errors++;
    }
}

// 4. Inserir dados padrão do pipeline
try {
    $pdo->exec("INSERT IGNORE INTO pipeline_stage_goals (stage, stage_label, max_hours, stage_order, color, icon) VALUES
        ('contato',    'Contato',       24,  1, '#9b59b6', 'fas fa-phone'),
        ('orcamento',  'Orçamento',     48,  2, '#3498db', 'fas fa-file-invoice-dollar'),
        ('venda',      'Venda',         24,  3, '#2ecc71', 'fas fa-handshake'),
        ('producao',   'Produção',      72,  4, '#e67e22', 'fas fa-industry'),
        ('preparacao', 'Preparação',    24,  5, '#1abc9c', 'fas fa-boxes-packing'),
        ('envio',      'Envio/Entrega', 48,  6, '#e74c3c', 'fas fa-truck'),
        ('financeiro', 'Financeiro',    48,  7, '#f39c12', 'fas fa-coins'),
        ('concluido',  'Concluído',      0,  8, '#27ae60', 'fas fa-check-double')
    ");
    echo "[OK] Dados do pipeline inseridos.\n";
} catch (PDOException $e) {
    echo "[INFO] Pipeline goals: " . $e->getMessage() . "\n";
}

// 5. Criar usuário admin
echo "\n--------------------------------------\n";
echo " Criando usuário administrador...\n";
echo "--------------------------------------\n";

$adminEmail = 'admin@admin.com';
$adminPassword = password_hash('admin123', PASSWORD_BCRYPT);
$adminName = 'Administrador';

try {
    // Verificar se já existe
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute([':email' => $adminEmail]);
    
    if ($stmt->rowCount() > 0) {
        // Atualizar senha e role do usuário existente
        $stmt = $pdo->prepare("UPDATE users SET password = :password, role = 'admin', name = :name WHERE email = :email");
        $stmt->execute([
            ':password' => $adminPassword,
            ':name' => $adminName,
            ':email' => $adminEmail,
        ]);
        echo "[OK] Usuário admin já existia — senha e role atualizados.\n";
    } else {
        // Criar novo
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role, created_at) VALUES (:name, :email, :password, 'admin', NOW())");
        $stmt->execute([
            ':name' => $adminName,
            ':email' => $adminEmail,
            ':password' => $adminPassword,
        ]);
        echo "[OK] Usuário admin criado com sucesso!\n";
    }

    echo "\n  Email: $adminEmail\n";
    echo "  Senha: admin123\n";
    echo "  Role:  admin\n";

} catch (PDOException $e) {
    echo "<span style='color:red'>[ERRO] Criar admin: " . $e->getMessage() . "</span>\n";
    $errors++;
}

// 6. Resumo
echo "\n======================================\n";
if ($errors === 0) {
    echo " ✅ SETUP CONCLUÍDO COM SUCESSO!\n";
} else {
    echo " ⚠️  SETUP CONCLUÍDO COM $errors ERRO(S)\n";
}
echo "======================================\n";
echo "\n⚠️  APAGUE este arquivo (setup.php) após executar!\n";
echo "\n<a href='/sistemaTiago/?page=login' style='color:#0ff;'>→ Ir para o Login</a>\n";
echo "</pre>";
