<div class="row">
    
</div>

<div class="row g-4 justify-content-center">
    <!-- Atalho Clientes -->
    <div class="col-md-3 col-sm-6">
        <a href="/sistemaTiago/?page=customers" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                    <h5 class="card-title text-dark">Clientes</h5>
                    <p class="card-text text-muted small">Gerenciar base de clientes</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Atalho Produtos -->
    <div class="col-md-3 col-sm-6">
        <a href="/sistemaTiago/?page=products" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-box-open fa-2x"></i>
                    </div>
                    <h5 class="card-title text-dark">Produtos</h5>
                    <p class="card-text text-muted small">Catálogo de serviços e estoque</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Atalho Pedidos -->
    <div class="col-md-3 col-sm-6">
        <a href="/sistemaTiago/?page=orders" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-shopping-cart fa-2x"></i>
                    </div>
                    <h5 class="card-title text-dark">Pedidos</h5>
                    <p class="card-text text-muted small">Vendas e Orçamentos</p>
                </div>
            </div>
        </a>
    </div>

    <?php if($_SESSION['user_role'] === 'admin'): ?>
    <!-- Atalho Usuários -->
    <div class="col-md-3 col-sm-6">
        <a href="/sistemaTiago/?page=users" class="text-decoration-none">
            <div class="card shadow-sm border-0 h-100 hover-card">
                <div class="card-body text-center p-4">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                        <i class="fas fa-user-cog fa-2x"></i>
                    </div>
                    <h5 class="card-title text-dark">Usuários</h5>
                    <p class="card-text text-muted small">Acessos e Permissões</p>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>
</div>

<style>
.hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>
