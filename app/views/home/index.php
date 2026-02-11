<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <button type="button" class="btn btn-sm btn-outline-secondary">Compartilhar</button>
            <button type="button" class="btn btn-sm btn-outline-secondary">Exportar</button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-header">Pedidos Pendentes</div>
            <div class="card-body">
                <h5 class="card-title">15</h5>
                <p class="card-text">Aguardando aprovação.</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-header">Faturamento Mensal</div>
            <div class="card-body">
                <h5 class="card-title">R$ 12.450,00</h5>
                <p class="card-text">Mês Atual</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-header">Em Produção</div>
            <div class="card-body">
                <h5 class="card-title">8</h5>
                <p class="card-text">Sendo processados.</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger mb-3">
            <div class="card-header">Alertas</div>
            <div class="card-body">
                <h5 class="card-title">3</h5>
                <p class="card-text">Estoque baixo.</p>
            </div>
        </div>
    </div>
</div>

<h2>Últimos Pedidos</h2>
<div class="table-responsive">
    <table class="table table-striped table-sm">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Cliente</th>
                <th scope="col">Produto</th>
                <th scope="col">Valor</th>
                <th scope="col">Status</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1,001</td>
                <td>Empresa X</td>
                <td>Cartões de Visita</td>
                <td>R$ 150,00</td>
                <td><span class="badge bg-success">Concluído</span></td>
            </tr>
            <tr>
                <td>1,002</td>
                <td>João da Silva</td>
                <td>Banner 1x1m</td>
                <td>R$ 80,00</td>
                <td><span class="badge bg-warning">Em Produção</span></td>
            </tr>
             <tr>
                <td>1,003</td>
                <td>Loja Y</td>
                <td>Panfletos A5</td>
                <td>R$ 400,00</td>
                <td><span class="badge bg-secondary">Pendente</span></td>
            </tr>
        </tbody>
    </table>
</div>
