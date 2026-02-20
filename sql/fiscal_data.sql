-- ═══════════════════════════════════════════════════════════════
-- MIGRAÇÃO: DADOS FISCAIS (Produtos + Empresa)
-- Campos necessários para emissão de NF-e
-- ═══════════════════════════════════════════════════════════════

-- ─────────────────────────────────────────────────────
-- 1. CAMPOS FISCAIS NO PRODUTO
-- ─────────────────────────────────────────────────────

ALTER TABLE products
    ADD COLUMN IF NOT EXISTS fiscal_ncm VARCHAR(10) DEFAULT NULL COMMENT 'NCM - Nomenclatura Comum do Mercosul (8 dígitos)',
    ADD COLUMN IF NOT EXISTS fiscal_cest VARCHAR(10) DEFAULT NULL COMMENT 'CEST - Código Especificador da Substituição Tributária (7 dígitos)',
    ADD COLUMN IF NOT EXISTS fiscal_cfop VARCHAR(10) DEFAULT NULL COMMENT 'CFOP - Código Fiscal de Operações e Prestações',
    ADD COLUMN IF NOT EXISTS fiscal_cst_icms VARCHAR(5) DEFAULT NULL COMMENT 'CST ICMS - Código de Situação Tributária do ICMS',
    ADD COLUMN IF NOT EXISTS fiscal_csosn VARCHAR(5) DEFAULT NULL COMMENT 'CSOSN - Código de Situação da Operação no Simples Nacional',
    ADD COLUMN IF NOT EXISTS fiscal_cst_pis VARCHAR(5) DEFAULT NULL COMMENT 'CST PIS - Código de Situação Tributária do PIS',
    ADD COLUMN IF NOT EXISTS fiscal_cst_cofins VARCHAR(5) DEFAULT NULL COMMENT 'CST COFINS - Código de Situação Tributária da COFINS',
    ADD COLUMN IF NOT EXISTS fiscal_cst_ipi VARCHAR(5) DEFAULT NULL COMMENT 'CST IPI - Código de Situação Tributária do IPI',
    ADD COLUMN IF NOT EXISTS fiscal_origem VARCHAR(2) DEFAULT '0' COMMENT 'Origem da mercadoria (0=Nacional, 1=Estrangeira importação direta, etc.)',
    ADD COLUMN IF NOT EXISTS fiscal_unidade VARCHAR(10) DEFAULT 'UN' COMMENT 'Unidade de medida fiscal (UN, KG, MT, M2, M3, etc.)',
    ADD COLUMN IF NOT EXISTS fiscal_ean VARCHAR(14) DEFAULT NULL COMMENT 'Código EAN/GTIN (código de barras)',
    ADD COLUMN IF NOT EXISTS fiscal_aliq_icms DECIMAL(5,2) DEFAULT NULL COMMENT 'Alíquota ICMS (%)',
    ADD COLUMN IF NOT EXISTS fiscal_aliq_ipi DECIMAL(5,2) DEFAULT NULL COMMENT 'Alíquota IPI (%)',
    ADD COLUMN IF NOT EXISTS fiscal_aliq_pis DECIMAL(5,4) DEFAULT NULL COMMENT 'Alíquota PIS (%)',
    ADD COLUMN IF NOT EXISTS fiscal_aliq_cofins DECIMAL(5,4) DEFAULT NULL COMMENT 'Alíquota COFINS (%)',
    ADD COLUMN IF NOT EXISTS fiscal_beneficio VARCHAR(20) DEFAULT NULL COMMENT 'Código de benefício fiscal (cBenef)',
    ADD COLUMN IF NOT EXISTS fiscal_info_adicional TEXT DEFAULT NULL COMMENT 'Informações adicionais do produto para a NF-e';

-- ─────────────────────────────────────────────────────
-- 2. DADOS FISCAIS DA EMPRESA (company_settings key-value)
-- Inserir valores padrão (não sobrescrevem se já existirem)
-- ─────────────────────────────────────────────────────

INSERT IGNORE INTO company_settings (setting_key, setting_value) VALUES
-- Identificação Fiscal
('fiscal_razao_social', ''),
('fiscal_nome_fantasia', ''),
('fiscal_cnpj', ''),
('fiscal_ie', ''),
('fiscal_im', ''),
('fiscal_cnae', ''),
('fiscal_crt', '1'),

-- Endereço Fiscal (pode diferir do endereço comercial)
('fiscal_endereco_logradouro', ''),
('fiscal_endereco_numero', ''),
('fiscal_endereco_complemento', ''),
('fiscal_endereco_bairro', ''),
('fiscal_endereco_cidade', ''),
('fiscal_endereco_uf', ''),
('fiscal_endereco_cep', ''),
('fiscal_endereco_cod_municipio', ''),
('fiscal_endereco_cod_pais', '1058'),
('fiscal_endereco_pais', 'Brasil'),
('fiscal_endereco_fone', ''),

-- Certificado Digital
('fiscal_certificado_tipo', 'A1'),
('fiscal_certificado_senha', ''),
('fiscal_certificado_validade', ''),

-- Ambiente e Série NF-e
('fiscal_ambiente', '2'),
('fiscal_serie_nfe', '1'),
('fiscal_proximo_numero_nfe', '1'),
('fiscal_modelo_nfe', '55'),
('fiscal_tipo_emissao', '1'),
('fiscal_finalidade', '1'),

-- Alíquotas Padrão da Empresa
('fiscal_aliq_icms_padrao', ''),
('fiscal_aliq_pis_padrao', '0.65'),
('fiscal_aliq_cofins_padrao', '3.00'),
('fiscal_aliq_iss_padrao', ''),

-- Natureza da Operação
('fiscal_nat_operacao', 'Venda de mercadoria'),

-- Informações Complementares padrão na NF-e
('fiscal_info_complementar', '');
