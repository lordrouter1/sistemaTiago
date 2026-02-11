<div class="container py-4">
    <h2 class="mb-4">Cadastro de Cliente</h2>
    <form id="customerForm" enctype="multipart/form-data" method="post" action="/sistemaTiago/?page=customers&action=store">
        <div class="row">
            <div class="col-md-3 text-center mb-4">
                <label for="photo" class="form-label fw-bold">Foto do Cliente</label>
                <div id="photoContainer" class="bg-white border rounded d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 200px; height: 200px; overflow: hidden; cursor: pointer;" onclick="document.getElementById('photo').click();">
                    <img id="photoPreview" src="" alt="Prévia" class="w-100 h-100" style="object-fit: cover; display: none;">
                    <div id="photoPlaceholder" class="text-secondary">
                        <i class="fas fa-camera fa-3x"></i>
                        <p class="small mt-2">Clique para adicionar</p>
                    </div>
                </div>
                <input class="form-control d-none" type="file" id="photo" name="photo" accept="image/*">
            </div>
            <div class="col-md-9">
                <fieldset class="border rounded p-3 mb-4">
                    <legend class="float-none w-auto px-2 fs-6">Dados Pessoais</legend>
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="name" class="form-label">Nome Completo / Razão Social <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-4">
                            <label for="document" class="form-label">CPF / CNPJ</label>
                            <input type="text" class="form-control" id="document" name="document">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Telefone / WhatsApp <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded p-3 mb-4">
                    <legend class="float-none w-auto px-2 fs-6">Endereço</legend>
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <label for="zipcode" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="zipcode" name="zipcode">
                        </div>
                        <div class="col-md-3">
                            <label for="address_type" class="form-label">Tipo Logradouro</label>
                            <select class="form-select" id="address_type" name="address_type">
                                <option value="">Selecione...</option>
                                <option value="Rua">Rua</option>
                                <option value="Avenida">Avenida</option>
                                <option value="Travessa">Travessa</option>
                                <option value="Praça">Praça</option>
                                <option value="Alameda">Alameda</option>
                                <option value="Rodovia">Rodovia</option>
                                <option value="Outro">Outro</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="address_name" class="form-label">Nome do Logradouro</label>
                            <input type="text" class="form-control" id="address_name" name="address_name">
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label for="address_number" class="form-label">Número</label>
                            <input type="text" class="form-control" id="address_number" name="address_number">
                        </div>
                        <div class="col-md-5">
                            <label for="neighborhood" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="neighborhood" name="neighborhood">
                        </div>
                        <div class="col-md-5">
                            <label for="complement" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="complement" name="complement">
                        </div>
                    </div>
                </fieldset>
                <fieldset class="border rounded p-3 mb-2">
                    <legend class="float-none w-auto px-2 fs-6">Observações</legend>
                    <div class="mb-3">
                        <textarea class="form-control" id="observations" name="observations" rows="3" style="resize: none; height: 80px; background: #f8f9fa;" readonly></textarea>
                    </div>
                </fieldset>
                <div class="text-end">
                    <a href="/sistemaTiago/?page=customers" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Cliente</button>
                </div>
            </div>
        </div>
    </form>
</div>
