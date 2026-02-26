-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 26/02/2026 às 19:44
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `akti_teste`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `catalog_links`
--

CREATE TABLE `catalog_links` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `show_prices` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `catalog_links`
--

INSERT INTO `catalog_links` (`id`, `order_id`, `token`, `show_prices`, `is_active`, `expires_at`, `created_at`) VALUES
(1, 1, '15887fa1928b723fc3a6ab97a0bcdc1022cc57674be17c166f8b276caeeb0f4b', 0, 0, '2026-02-25 13:28:55', '2026-02-18 12:28:55'),
(2, 1, '78fb2330975e17b6f925473a430a0cb8516106af2baccb29631bd3ab904c605d', 1, 1, '2026-02-25 13:33:11', '2026-02-18 12:33:11'),
(3, 6, '9fbd6838ba5a1b47c76a3c0bf21d9ab46e756ae94b572b635779f766b88f9d0c', 0, 1, '2026-02-26 20:58:38', '2026-02-19 19:58:38'),
(4, 7, '14a35020692c5359700e294a916a0786e8c89339ba17d294f9b890adcc6f1fe8', 0, 0, '2026-03-02 12:22:28', '2026-02-23 11:22:28'),
(5, 7, '55c7128e647383223e1c1694dbf6b1c077aedb921b7fc464b893fff7b6251195', 0, 0, '2026-03-02 12:59:25', '2026-02-23 11:59:25'),
(6, 7, 'd84bb67e8cfe579424fb0264e736b7c8857ccb6b910c3eec8983dba9e21a6676', 0, 1, '2026-03-02 17:47:57', '2026-02-23 16:47:57');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `categories`
--

INSERT INTO `categories` (`id`, `name`) VALUES
(1, 'Folhas'),
(3, 'personalizáveis '),
(4, 'roupas');

-- --------------------------------------------------------

--
-- Estrutura para tabela `category_grades`
--

CREATE TABLE `category_grades` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `grade_type_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `category_grades`
--

INSERT INTO `category_grades` (`id`, `category_id`, `grade_type_id`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 4, 1, 0, 1, '2026-02-20 19:04:17'),
(2, 4, 2, 1, 1, '2026-02-20 19:04:17');

-- --------------------------------------------------------

--
-- Estrutura para tabela `category_grade_combinations`
--

CREATE TABLE `category_grade_combinations` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `combination_key` varchar(255) NOT NULL,
  `combination_label` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `category_grade_combinations`
--

INSERT INTO `category_grade_combinations` (`id`, `category_id`, `combination_key`, `combination_label`, `is_active`, `created_at`) VALUES
(1, 4, '1:1|2:5', 'Tamanho: P / Cor: Branco', 1, '2026-02-20 19:04:18'),
(2, 4, '1:1|2:6', 'Tamanho: P / Cor: Preto', 1, '2026-02-20 19:04:18'),
(3, 4, '1:1|2:7', 'Tamanho: P / Cor: Amarelo', 1, '2026-02-20 19:04:18'),
(4, 4, '1:2|2:5', 'Tamanho: M / Cor: Branco', 1, '2026-02-20 19:04:18'),
(5, 4, '1:2|2:6', 'Tamanho: M / Cor: Preto', 1, '2026-02-20 19:04:18'),
(6, 4, '1:2|2:7', 'Tamanho: M / Cor: Amarelo', 1, '2026-02-20 19:04:18'),
(7, 4, '1:3|2:5', 'Tamanho: G / Cor: Branco', 1, '2026-02-20 19:04:18'),
(8, 4, '1:3|2:6', 'Tamanho: G / Cor: Preto', 1, '2026-02-20 19:04:18'),
(9, 4, '1:3|2:7', 'Tamanho: G / Cor: Amarelo', 1, '2026-02-20 19:04:18'),
(10, 4, '1:4|2:5', 'Tamanho: GG / Cor: Branco', 1, '2026-02-20 19:04:19'),
(11, 4, '1:4|2:6', 'Tamanho: GG / Cor: Preto', 1, '2026-02-20 19:04:19'),
(12, 4, '1:4|2:7', 'Tamanho: GG / Cor: Amarelo', 1, '2026-02-20 19:04:19');

-- --------------------------------------------------------

--
-- Estrutura para tabela `category_grade_values`
--

CREATE TABLE `category_grade_values` (
  `id` int(11) NOT NULL,
  `category_grade_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `category_grade_values`
--

INSERT INTO `category_grade_values` (`id`, `category_grade_id`, `value`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 1, 'P', 0, 1, '2026-02-20 19:04:17'),
(2, 1, 'M', 1, 1, '2026-02-20 19:04:17'),
(3, 1, 'G', 2, 1, '2026-02-20 19:04:17'),
(4, 1, 'GG', 3, 1, '2026-02-20 19:04:17'),
(5, 2, 'Branco', 0, 1, '2026-02-20 19:04:18'),
(6, 2, 'Preto', 1, 1, '2026-02-20 19:04:18'),
(7, 2, 'Amarelo', 2, 1, '2026-02-20 19:04:18');

-- --------------------------------------------------------

--
-- Estrutura para tabela `category_sectors`
--

CREATE TABLE `category_sectors` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `category_sectors`
--

INSERT INTO `category_sectors` (`id`, `category_id`, `sector_id`, `sort_order`) VALUES
(1, 1, 1, 0),
(2, 1, 2, 1),
(6, 3, 5, 0),
(7, 3, 1, 1),
(8, 3, 3, 2),
(9, 3, 4, 3),
(10, 4, 2, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `company_settings`
--

CREATE TABLE `company_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `company_settings`
--

INSERT INTO `company_settings` (`id`, `setting_key`, `setting_value`, `updated_at`) VALUES
(1, 'boleto_banco', '001', '2026-02-20 17:27:00'),
(2, 'boleto_agencia', '0012', '2026-02-20 17:27:00'),
(3, 'boleto_agencia_dv', '2', '2026-02-20 17:27:00'),
(4, 'boleto_conta', '12345678', '2026-02-20 17:27:01'),
(5, 'boleto_conta_dv', '9', '2026-02-20 17:27:01'),
(6, 'boleto_carteira', '109', '2026-02-20 17:27:01'),
(7, 'boleto_especie', 'R$', '2026-02-20 17:27:01'),
(8, 'boleto_cedente', 'julio cesar benin kronhardt', '2026-02-20 17:27:01'),
(9, 'boleto_cedente_documento', '02694062040', '2026-02-20 17:27:01'),
(10, 'boleto_convenio', '1234567', '2026-02-20 17:27:01'),
(11, 'boleto_nosso_numero', '1', '2026-02-20 17:27:01'),
(12, 'boleto_nosso_numero_digitos', '7', '2026-02-20 17:27:01'),
(13, 'boleto_instrucoes', 'Não receber após o vencimento.\r\nMulta de 2% após o vencimento.\r\nJuros de 1% ao mês.', '2026-02-20 17:27:01'),
(14, 'boleto_multa', '2.00', '2026-02-20 17:27:02'),
(15, 'boleto_juros', '1.00', '2026-02-20 17:27:02'),
(16, 'boleto_aceite', 'S', '2026-02-20 17:27:02'),
(17, 'boleto_especie_doc', 'DM', '2026-02-20 17:27:02'),
(18, 'boleto_demonstrativo', '', '2026-02-20 17:27:02'),
(19, 'boleto_local_pagamento', 'Pagável em qualquer banco até o vencimento', '2026-02-20 17:27:02'),
(20, 'boleto_cedente_endereco', '', '2026-02-20 17:27:02'),
(21, 'fiscal_razao_social', '', '2026-02-20 19:18:37'),
(22, 'fiscal_nome_fantasia', '', '2026-02-20 19:18:37'),
(23, 'fiscal_cnpj', '', '2026-02-20 19:18:37'),
(24, 'fiscal_ie', '', '2026-02-20 19:18:37'),
(25, 'fiscal_im', '', '2026-02-20 19:18:37'),
(26, 'fiscal_cnae', '', '2026-02-20 19:18:37'),
(27, 'fiscal_crt', '1', '2026-02-20 19:18:37'),
(28, 'fiscal_endereco_logradouro', '', '2026-02-20 19:18:37'),
(29, 'fiscal_endereco_numero', '', '2026-02-20 19:18:37'),
(30, 'fiscal_endereco_complemento', '', '2026-02-20 19:18:37'),
(31, 'fiscal_endereco_bairro', '', '2026-02-20 19:18:37'),
(32, 'fiscal_endereco_cidade', '', '2026-02-20 19:18:37'),
(33, 'fiscal_endereco_uf', '', '2026-02-20 19:18:37'),
(34, 'fiscal_endereco_cep', '', '2026-02-20 19:18:37'),
(35, 'fiscal_endereco_cod_municipio', '', '2026-02-20 19:18:37'),
(36, 'fiscal_endereco_cod_pais', '1058', '2026-02-20 19:18:37'),
(37, 'fiscal_endereco_pais', 'Brasil', '2026-02-20 19:18:37'),
(38, 'fiscal_endereco_fone', '', '2026-02-20 19:18:37'),
(39, 'fiscal_certificado_tipo', 'A1', '2026-02-20 19:18:37'),
(40, 'fiscal_certificado_senha', '', '2026-02-20 19:18:37'),
(41, 'fiscal_certificado_validade', '', '2026-02-20 19:18:37'),
(42, 'fiscal_ambiente', '2', '2026-02-20 19:18:37'),
(43, 'fiscal_serie_nfe', '1', '2026-02-20 19:18:37'),
(44, 'fiscal_proximo_numero_nfe', '1', '2026-02-20 19:18:37'),
(45, 'fiscal_modelo_nfe', '55', '2026-02-20 19:18:37'),
(46, 'fiscal_tipo_emissao', '1', '2026-02-20 19:18:37'),
(47, 'fiscal_finalidade', '1', '2026-02-20 19:18:37'),
(48, 'fiscal_aliq_icms_padrao', '', '2026-02-20 19:18:37'),
(49, 'fiscal_aliq_pis_padrao', '0.65', '2026-02-20 19:18:37'),
(50, 'fiscal_aliq_cofins_padrao', '3.00', '2026-02-20 19:18:37'),
(51, 'fiscal_aliq_iss_padrao', '', '2026-02-20 19:18:37'),
(52, 'fiscal_nat_operacao', 'Venda de mercadoria', '2026-02-20 19:18:37'),
(53, 'fiscal_info_complementar', '', '2026-02-20 19:18:37'),
(54, 'company_name', 'Empresa de teste', '2026-02-26 12:34:06'),
(55, 'company_document', '', '2026-02-26 12:34:06'),
(56, 'company_phone', '', '2026-02-26 12:34:06'),
(57, 'company_email', '', '2026-02-26 12:34:06'),
(58, 'company_website', '', '2026-02-26 12:34:06'),
(59, 'company_zipcode', '', '2026-02-26 12:34:06'),
(60, 'company_address_type', 'Rua', '2026-02-26 12:34:06'),
(61, 'company_address_name', '', '2026-02-26 12:34:06'),
(62, 'company_address_number', '', '2026-02-26 12:34:06'),
(63, 'company_neighborhood', '', '2026-02-26 12:34:07'),
(64, 'company_complement', '', '2026-02-26 12:34:07'),
(65, 'company_city', '', '2026-02-26 12:34:07'),
(66, 'company_state', '', '2026-02-26 12:34:07'),
(67, 'quote_validity_days', '15', '2026-02-26 12:34:07'),
(68, 'quote_footer_note', '', '2026-02-26 12:34:07'),
(69, 'company_logo', 'assets/uploads/company_logo_1772109247.webp', '2026-02-26 12:34:07');

-- --------------------------------------------------------

--
-- Estrutura para tabela `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `document` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `photo` varchar(255) DEFAULT NULL,
  `price_table_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `customers`
--

INSERT INTO `customers` (`id`, `name`, `email`, `phone`, `document`, `address`, `created_at`, `photo`, `price_table_id`) VALUES
(2, 'julio cesar benin kronhardt', 'juliobenin@yahoo.com.br', '(54) 99999-4316', '026.940.620-40', '{\"zipcode\":\"99200-000\",\"address_type\":\"Rua\",\"address_name\":\"marechal floriano\",\"address_number\":\"1380\",\"neighborhood\":\"centro\",\"complement\":\"casa 1\"}', '2026-02-12 11:50:59', NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `group_permissions`
--

CREATE TABLE `group_permissions` (
  `id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `page_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `group_permissions`
--

INSERT INTO `group_permissions` (`id`, `group_id`, `page_name`) VALUES
(6, 7, 'dashboard'),
(7, 7, 'orders'),
(8, 7, 'pipeline');

-- --------------------------------------------------------

--
-- Estrutura para tabela `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `price_table_id` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('orcamento','pendente','Pendente','aprovado','em_producao','concluido','cancelado') DEFAULT 'orcamento',
  `pipeline_stage` enum('contato','orcamento','venda','producao','preparacao','envio','financeiro','concluido') DEFAULT 'contato',
  `pipeline_entered_at` datetime DEFAULT current_timestamp(),
  `deadline` date DEFAULT NULL,
  `priority` enum('baixa','normal','alta','urgente') DEFAULT 'normal',
  `internal_notes` text DEFAULT NULL,
  `quote_notes` text DEFAULT NULL,
  `scheduled_date` date DEFAULT NULL,
  `assigned_to` int(11) DEFAULT NULL,
  `payment_status` enum('pendente','parcial','pago') DEFAULT 'pendente',
  `payment_method` varchar(50) DEFAULT NULL,
  `installments` int(11) DEFAULT NULL,
  `installment_value` decimal(10,2) DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT 0.00,
  `shipping_type` enum('retirada','entrega','correios') DEFAULT 'retirada',
  `shipping_address` text DEFAULT NULL,
  `tracking_code` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `price_table_id`, `total_amount`, `status`, `pipeline_stage`, `pipeline_entered_at`, `deadline`, `priority`, `internal_notes`, `quote_notes`, `scheduled_date`, `assigned_to`, `payment_status`, `payment_method`, `installments`, `installment_value`, `discount`, `shipping_type`, `shipping_address`, `tracking_code`, `created_at`) VALUES
(1, 2, NULL, 0.05, 'cancelado', '', '2026-02-20 14:33:37', NULL, 'normal', '', '', NULL, NULL, 'pendente', '', NULL, NULL, 0.00, 'retirada', '', '', '2026-02-12 11:57:22'),
(2, 2, NULL, 1225.00, 'concluido', 'concluido', '2026-02-18 15:02:14', NULL, 'normal', '', '', NULL, NULL, 'pendente', '', NULL, NULL, 0.00, 'retirada', '', '', '2026-02-18 17:12:07'),
(3, 2, NULL, 245.00, 'cancelado', '', '2026-02-20 14:33:28', NULL, 'normal', '', '', NULL, NULL, 'pendente', '', NULL, NULL, 0.00, 'retirada', '', '', '2026-02-18 19:01:54'),
(4, 2, NULL, 25.00, 'cancelado', '', '2026-02-20 14:33:20', NULL, 'normal', 'teste', NULL, NULL, NULL, 'pendente', NULL, NULL, NULL, 0.00, 'retirada', NULL, NULL, '2026-02-18 19:28:09'),
(5, 2, NULL, 300.00, 'cancelado', '', '2026-02-20 14:33:24', NULL, 'urgente', 'teste', NULL, NULL, NULL, 'pendente', NULL, NULL, NULL, 0.00, 'retirada', NULL, NULL, '2026-02-18 19:53:16'),
(6, 2, NULL, 87.00, 'cancelado', '', '2026-02-20 14:33:33', NULL, 'normal', '', '', NULL, NULL, 'pendente', '', NULL, NULL, 0.00, 'retirada', '', '', '2026-02-19 19:58:03'),
(7, 2, NULL, 10.00, 'aprovado', 'venda', '2026-02-26 10:54:38', NULL, 'normal', '', NULL, NULL, NULL, 'pendente', NULL, NULL, NULL, 0.00, 'retirada', NULL, NULL, '2026-02-23 11:20:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_extra_costs`
--

CREATE TABLE `order_extra_costs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_extra_costs`
--

INSERT INTO `order_extra_costs` (`id`, `order_id`, `description`, `amount`, `created_at`) VALUES
(1, 1, 'desconto venda', -0.10, '2026-02-18 12:47:54');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `grade_combination_id` int(11) DEFAULT NULL,
  `grade_description` varchar(500) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `grade_combination_id`, `grade_description`, `quantity`, `unit_price`, `subtotal`) VALUES
(2, 1, 1, NULL, NULL, 3, 0.05, 0.15),
(3, 2, 2, NULL, NULL, 1, 25.00, 25.00),
(4, 2, 3, NULL, NULL, 100, 12.00, 1200.00),
(5, 3, 2, NULL, NULL, 5, 25.00, 125.00),
(6, 3, 3, NULL, NULL, 10, 12.00, 120.00),
(7, 5, 1, NULL, NULL, 1000, 0.05, 50.00),
(8, 5, 2, NULL, NULL, 10, 25.00, 250.00),
(9, 6, 2, NULL, NULL, 3, 25.00, 75.00),
(10, 6, 3, NULL, NULL, 1, 12.00, 12.00),
(12, 7, 4, 11, 'Tamanho: GG / Cor: Preto', 1, 10.00, 10.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_item_logs`
--

CREATE TABLE `order_item_logs` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `file_path` varchar(500) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_item_logs`
--

INSERT INTO `order_item_logs` (`id`, `order_id`, `order_item_id`, `user_id`, `message`, `file_path`, `file_name`, `file_type`, `created_at`) VALUES
(1, 3, 5, 1, 'impressão', '/sistemaTiago/assets/uploads/item_logs/3/5/1771442203_5121cb58.webp', '1_H90ZEKu0LMMIp96R6jbH3w.webp', 'image/webp', '2026-02-18 16:16:43'),
(2, 3, 6, 1, 'escrever: Julio Cesar', NULL, NULL, NULL, '2026-02-18 16:17:03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_preparation_checklist`
--

CREATE TABLE `order_preparation_checklist` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `check_key` varchar(100) NOT NULL,
  `checked` tinyint(1) DEFAULT 0,
  `checked_by` int(11) DEFAULT NULL,
  `checked_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_preparation_checklist`
--

INSERT INTO `order_preparation_checklist` (`id`, `order_id`, `check_key`, `checked`, `checked_by`, `checked_at`, `created_at`) VALUES
(1, 1, 'revisao_arquivos', 1, 1, '2026-02-19 16:23:25', '2026-02-19 16:23:25'),
(2, 1, 'corte_acabamento', 1, 1, '2026-02-19 16:23:30', '2026-02-19 16:23:30'),
(3, 1, 'embalagem', 1, 1, '2026-02-19 16:23:33', '2026-02-19 16:23:33'),
(4, 1, 'conferencia_qtd', 1, 1, '2026-02-19 16:23:35', '2026-02-19 16:23:35'),
(5, 1, 'conferencia_qual', 1, 1, '2026-02-19 16:23:37', '2026-02-19 16:23:37'),
(6, 1, 'pronto_envio', 1, 1, '2026-02-19 17:24:44', '2026-02-19 16:23:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `order_production_sectors`
--

CREATE TABLE `order_production_sectors` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `order_item_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `status` enum('pendente','em_andamento','concluido') DEFAULT 'pendente',
  `sort_order` int(11) DEFAULT 0,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `completed_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `order_production_sectors`
--

INSERT INTO `order_production_sectors` (`id`, `order_id`, `order_item_id`, `sector_id`, `status`, `sort_order`, `started_at`, `completed_at`, `completed_by`) VALUES
(1, 1, 2, 1, 'concluido', 0, '2026-02-18 16:01:03', '2026-02-18 16:01:03', 1),
(2, 1, 2, 2, 'concluido', 1, '2026-02-20 07:36:39', '2026-02-20 07:36:39', 1),
(3, 2, 3, 1, 'concluido', 0, '2026-02-18 14:30:47', '2026-02-18 14:31:12', 1),
(4, 2, 3, 3, 'pendente', 1, NULL, NULL, NULL),
(5, 2, 3, 4, 'pendente', 2, NULL, NULL, NULL),
(6, 2, 4, 1, 'concluido', 0, '2026-02-18 14:31:00', '2026-02-18 14:31:19', 1),
(7, 2, 4, 3, 'concluido', 1, '2026-02-18 14:34:25', '2026-02-18 14:34:28', 1),
(8, 2, 4, 4, 'pendente', 2, NULL, NULL, NULL),
(9, 3, 5, 5, 'pendente', 0, NULL, NULL, NULL),
(10, 3, 5, 1, 'pendente', 1, NULL, NULL, NULL),
(11, 3, 5, 3, 'pendente', 2, NULL, NULL, NULL),
(12, 3, 5, 4, 'pendente', 3, NULL, NULL, NULL),
(13, 3, 6, 5, 'concluido', 0, '2026-02-19 14:53:25', '2026-02-19 14:53:25', 1),
(14, 3, 6, 1, 'pendente', 1, NULL, NULL, NULL),
(15, 3, 6, 3, 'pendente', 2, NULL, NULL, NULL),
(16, 3, 6, 4, 'pendente', 3, NULL, NULL, NULL),
(17, 6, 9, 5, 'concluido', 0, '2026-02-19 16:59:59', '2026-02-19 16:59:59', 1),
(18, 6, 9, 1, 'pendente', 1, NULL, NULL, NULL),
(19, 6, 9, 3, 'pendente', 2, NULL, NULL, NULL),
(20, 6, 9, 4, 'pendente', 3, NULL, NULL, NULL),
(21, 6, 10, 5, 'pendente', 0, NULL, NULL, NULL),
(22, 6, 10, 1, 'pendente', 1, NULL, NULL, NULL),
(23, 6, 10, 3, 'pendente', 2, NULL, NULL, NULL),
(24, 6, 10, 4, 'pendente', 3, NULL, NULL, NULL),
(25, 7, 12, 2, 'pendente', 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `pipeline_history`
--

CREATE TABLE `pipeline_history` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `from_stage` varchar(30) DEFAULT NULL,
  `to_stage` varchar(30) NOT NULL,
  `changed_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pipeline_history`
--

INSERT INTO `pipeline_history` (`id`, `order_id`, `from_stage`, `to_stage`, `changed_by`, `notes`, `created_at`) VALUES
(1, 1, NULL, 'contato', 1, 'Pedido criado', '2026-02-12 11:57:23'),
(2, 1, 'contato', 'orcamento', 1, '', '2026-02-12 11:58:03'),
(3, 1, 'orcamento', 'producao', 1, '', '2026-02-18 11:27:16'),
(4, 1, 'producao', 'orcamento', 1, '', '2026-02-18 11:47:51'),
(5, 1, 'orcamento', 'venda', 1, '', '2026-02-18 12:36:11'),
(6, 1, 'venda', 'producao', 1, '', '2026-02-18 13:37:56'),
(7, 2, NULL, 'orcamento', 1, 'Pedido criado como Orçamento', '2026-02-18 17:12:07'),
(8, 2, 'orcamento', 'venda', 1, '', '2026-02-18 17:13:03'),
(9, 2, 'venda', 'producao', 1, '', '2026-02-18 17:13:08'),
(10, 2, 'producao', 'venda', 1, '', '2026-02-18 17:14:28'),
(11, 2, 'venda', 'producao', 1, '', '2026-02-18 17:15:12'),
(12, 2, 'producao', 'venda', 1, '', '2026-02-18 17:29:45'),
(13, 2, 'venda', 'producao', 1, '', '2026-02-18 17:30:26'),
(14, 2, 'producao', 'concluido', 1, '', '2026-02-18 18:02:14'),
(15, 3, NULL, 'orcamento', 1, 'Pedido criado como Orçamento', '2026-02-18 19:01:54'),
(16, 3, 'orcamento', 'venda', 1, '', '2026-02-18 19:02:07'),
(17, 3, 'venda', 'producao', 1, '', '2026-02-18 19:02:59'),
(18, 1, 'producao', 'venda', 1, '', '2026-02-18 19:15:31'),
(19, 1, 'venda', 'producao', 1, '', '2026-02-18 19:16:04'),
(20, 1, 'producao', 'venda', 1, '', '2026-02-18 19:17:51'),
(21, 1, 'venda', 'orcamento', 1, '', '2026-02-18 19:18:03'),
(22, 4, NULL, 'orcamento', 1, 'Pedido criado como Orçamento', '2026-02-18 19:28:09'),
(23, 4, 'orcamento', 'contato', 1, '', '2026-02-18 19:28:33'),
(24, 1, 'orcamento', 'venda', 1, '', '2026-02-18 19:47:14'),
(25, 5, NULL, 'orcamento', 1, 'Pedido criado como Orçamento', '2026-02-18 19:53:17'),
(26, 1, 'venda', 'preparacao', 1, '', '2026-02-19 18:45:22'),
(27, 6, NULL, 'contato', 1, 'Pedido criado como Contato', '2026-02-19 19:58:03'),
(28, 6, 'contato', 'orcamento', 1, '', '2026-02-19 19:58:21'),
(29, 6, 'orcamento', 'venda', 1, '', '2026-02-19 19:59:35'),
(30, 6, 'venda', 'producao', 1, '', '2026-02-19 19:59:44'),
(31, 1, 'preparacao', 'envio', 1, '', '2026-02-20 10:37:46'),
(32, 1, 'envio', 'financeiro', 1, '', '2026-02-20 13:38:23'),
(33, 4, 'contato', 'cancelado', 1, '', '2026-02-20 17:33:20'),
(34, 5, 'orcamento', 'cancelado', 1, '', '2026-02-20 17:33:24'),
(35, 3, 'producao', 'cancelado', 1, '', '2026-02-20 17:33:29'),
(36, 6, 'producao', 'cancelado', 1, '', '2026-02-20 17:33:33'),
(37, 1, 'financeiro', 'cancelado', 1, '', '2026-02-20 17:33:37'),
(38, 7, NULL, 'orcamento', 1, 'Pedido criado como Orçamento', '2026-02-23 11:20:54'),
(39, 7, 'orcamento', 'venda', 1, '', '2026-02-23 12:04:26'),
(40, 7, 'venda', 'producao', 1, '', '2026-02-23 12:04:31'),
(41, 7, 'producao', 'preparacao', 1, '', '2026-02-23 14:29:48'),
(42, 7, 'preparacao', 'producao', 1, '', '2026-02-23 16:47:16'),
(43, 7, 'producao', 'venda', 1, '', '2026-02-23 16:47:38'),
(44, 7, 'venda', 'orcamento', 1, '', '2026-02-23 16:47:51'),
(45, 7, 'orcamento', 'venda', 1, '', '2026-02-26 13:54:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `pipeline_stage_goals`
--

CREATE TABLE `pipeline_stage_goals` (
  `id` int(11) NOT NULL,
  `stage` varchar(30) NOT NULL,
  `stage_label` varchar(50) NOT NULL,
  `max_hours` int(11) NOT NULL DEFAULT 24,
  `stage_order` int(11) NOT NULL DEFAULT 0,
  `color` varchar(20) DEFAULT '#3498db',
  `icon` varchar(50) DEFAULT 'fas fa-circle',
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `pipeline_stage_goals`
--

INSERT INTO `pipeline_stage_goals` (`id`, `stage`, `stage_label`, `max_hours`, `stage_order`, `color`, `icon`, `is_active`, `updated_at`) VALUES
(1, 'contato', 'Contato', 24, 1, '#9b59b6', 'fas fa-phone', 1, '2026-02-12 11:39:51'),
(2, 'orcamento', 'Orçamento', 48, 2, '#3498db', 'fas fa-file-invoice-dollar', 1, '2026-02-12 11:41:56'),
(3, 'venda', 'Venda', 24, 3, '#2ecc71', 'fas fa-handshake', 1, '2026-02-12 11:39:51'),
(4, 'producao', 'Produção', 72, 4, '#e67e22', 'fas fa-industry', 1, '2026-02-12 11:41:56'),
(5, 'preparacao', 'Preparação', 24, 5, '#1abc9c', 'fas fa-boxes-packing', 1, '2026-02-12 11:41:56'),
(6, 'envio', 'Envio/Entrega', 48, 6, '#e74c3c', 'fas fa-truck', 1, '2026-02-12 11:39:51'),
(7, 'financeiro', 'Financeiro', 48, 7, '#f39c12', 'fas fa-coins', 1, '2026-02-12 11:39:51'),
(8, 'concluido', 'Concluído', 0, 8, '#27ae60', 'fas fa-check-double', 1, '2026-02-12 11:41:56');

-- --------------------------------------------------------

--
-- Estrutura para tabela `preparation_steps`
--

CREATE TABLE `preparation_steps` (
  `id` int(11) NOT NULL,
  `step_key` varchar(100) NOT NULL,
  `label` varchar(255) NOT NULL,
  `description` varchar(500) DEFAULT '',
  `icon` varchar(100) DEFAULT 'fas fa-check',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `preparation_steps`
--

INSERT INTO `preparation_steps` (`id`, `step_key`, `label`, `description`, `icon`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 'corte_acabamento', 'Revisão e Acabamento', 'Realizar corte, dobra e acabamento dos materiais', 'fas fa-cut', 1, 1, '2026-02-19 16:49:54', '2026-02-19 17:10:10'),
(3, 'embalagem', 'Embalagem', 'Embalar os produtos para envio/retirada', 'fas fa-box', 2, 1, '2026-02-19 16:49:54', '2026-02-19 17:09:45'),
(4, 'conferencia_qtd', 'Conferência de Quantidade', 'Verificar se a quantidade confere com o pedido', 'fas fa-list-check', 3, 1, '2026-02-19 16:49:54', '2026-02-19 17:09:50'),
(5, 'conferencia_qual', 'Conferência de Qualidade', 'Inspecionar qualidade final de todos os itens', 'fas fa-search', 4, 1, '2026-02-19 16:49:54', '2026-02-19 17:09:55'),
(6, 'pronto_envio', 'Pronto para Envio', 'Confirmar que o pedido está 100% pronto para envio', 'fas fa-truck-loading', 5, 1, '2026-02-19 16:49:54', '2026-02-19 17:10:01');

-- --------------------------------------------------------

--
-- Estrutura para tabela `price_tables`
--

CREATE TABLE `price_tables` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `price_tables`
--

INSERT INTO `price_tables` (`id`, `name`, `description`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 'Tabela Padr??o', 'Tabela de pre??os padr??o do sistema', 1, '2026-02-18 11:01:47', '2026-02-18 11:01:47');

-- --------------------------------------------------------

--
-- Estrutura para tabela `price_table_items`
--

CREATE TABLE `price_table_items` (
  `id` int(11) NOT NULL,
  `price_table_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `price_table_items`
--

INSERT INTO `price_table_items` (`id`, `price_table_id`, `product_id`, `price`, `created_at`) VALUES
(1, 1, 2, 25.00, '2026-02-18 17:08:01'),
(3, 1, 3, 12.00, '2026-02-18 17:09:40');

-- --------------------------------------------------------

--
-- Estrutura para tabela `production_sectors`
--

CREATE TABLE `production_sectors` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-cogs',
  `color` varchar(20) DEFAULT '#6c757d',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `production_sectors`
--

INSERT INTO `production_sectors` (`id`, `name`, `description`, `icon`, `color`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'Impressão', 'setor de impressão de produtos', 'fas fa-print', '#6a94b9', 0, 1, '2026-02-18 11:47:09'),
(2, 'Corte', '', 'fas fa-cut', '#0f3e18', 1, 1, '2026-02-18 12:38:47'),
(3, 'sublimação', '', 'fas fa-fire', '#5f9334', 0, 1, '2026-02-18 17:10:50'),
(4, 'Acabamentos', '', 'fas fa-paint-brush', '#75296f', 0, 1, '2026-02-18 17:11:22'),
(5, 'Desenho', '', 'fas fa-drafting-compass', '#bb5d67', 0, 1, '2026-02-18 17:37:08');

-- --------------------------------------------------------

--
-- Estrutura para tabela `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `subcategory_id` int(11) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `use_stock_control` tinyint(1) DEFAULT 0 COMMENT 'Se ativado e houver estoque, pedido não vai para produção',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `fiscal_ncm` varchar(10) DEFAULT NULL COMMENT 'NCM - Nomenclatura Comum do Mercosul (8 d├¡gitos)',
  `fiscal_cest` varchar(10) DEFAULT NULL COMMENT 'CEST - C├│digo Especificador da Substitui├º├úo Tribut├íria (7 d├¡gitos)',
  `fiscal_cfop` varchar(10) DEFAULT NULL COMMENT 'CFOP - C├│digo Fiscal de Opera├º├Áes e Presta├º├Áes',
  `fiscal_cst_icms` varchar(5) DEFAULT NULL COMMENT 'CST ICMS - C├│digo de Situa├º├úo Tribut├íria do ICMS',
  `fiscal_csosn` varchar(5) DEFAULT NULL COMMENT 'CSOSN - C├│digo de Situa├º├úo da Opera├º├úo no Simples Nacional',
  `fiscal_cst_pis` varchar(5) DEFAULT NULL COMMENT 'CST PIS - C├│digo de Situa├º├úo Tribut├íria do PIS',
  `fiscal_cst_cofins` varchar(5) DEFAULT NULL COMMENT 'CST COFINS - C├│digo de Situa├º├úo Tribut├íria da COFINS',
  `fiscal_cst_ipi` varchar(5) DEFAULT NULL COMMENT 'CST IPI - C├│digo de Situa├º├úo Tribut├íria do IPI',
  `fiscal_origem` varchar(2) DEFAULT '0' COMMENT 'Origem da mercadoria (0=Nacional, 1=Estrangeira importa├º├úo direta, etc.)',
  `fiscal_unidade` varchar(10) DEFAULT 'UN' COMMENT 'Unidade de medida fiscal (UN, KG, MT, M2, M3, etc.)',
  `fiscal_ean` varchar(14) DEFAULT NULL COMMENT 'C├│digo EAN/GTIN (c├│digo de barras)',
  `fiscal_aliq_icms` decimal(5,2) DEFAULT NULL COMMENT 'Al├¡quota ICMS (%)',
  `fiscal_aliq_ipi` decimal(5,2) DEFAULT NULL COMMENT 'Al├¡quota IPI (%)',
  `fiscal_aliq_pis` decimal(5,4) DEFAULT NULL COMMENT 'Al├¡quota PIS (%)',
  `fiscal_aliq_cofins` decimal(5,4) DEFAULT NULL COMMENT 'Al├¡quota COFINS (%)',
  `fiscal_beneficio` varchar(20) DEFAULT NULL COMMENT 'C├│digo de benef├¡cio fiscal (cBenef)',
  `fiscal_info_adicional` text DEFAULT NULL COMMENT 'Informa├º├Áes adicionais do produto para a NF-e'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `category_id`, `subcategory_id`, `price`, `stock_quantity`, `use_stock_control`, `created_at`, `fiscal_ncm`, `fiscal_cest`, `fiscal_cfop`, `fiscal_cst_icms`, `fiscal_csosn`, `fiscal_cst_pis`, `fiscal_cst_cofins`, `fiscal_cst_ipi`, `fiscal_origem`, `fiscal_unidade`, `fiscal_ean`, `fiscal_aliq_icms`, `fiscal_aliq_ipi`, `fiscal_aliq_pis`, `fiscal_aliq_cofins`, `fiscal_beneficio`, `fiscal_info_adicional`) VALUES
(1, 'Folha A4', 'folha A4 padrão', 1, 1, 0.05, 200, 0, '2026-02-11 20:15:21', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'UN', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'caneca', '', 3, 3, 25.00, 99, 0, '2026-02-18 17:08:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'UN', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'canetas', '', 3, 4, 12.00, 500, 0, '2026-02-18 17:09:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'UN', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'camisa polo', 'camisa polo', 4, 5, 10.00, 0, 0, '2026-02-20 19:05:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '0', 'UN', NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_grades`
--

CREATE TABLE `product_grades` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `grade_type_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `product_grades`
--

INSERT INTO `product_grades` (`id`, `product_id`, `grade_type_id`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 4, 1, 0, 1, '2026-02-20 19:05:32'),
(2, 4, 2, 1, 1, '2026-02-20 19:05:33');

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_grade_combinations`
--

CREATE TABLE `product_grade_combinations` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `combination_key` varchar(255) NOT NULL COMMENT 'Chave serializada ex: "2:5|3:8" (grade_id:value_id)',
  `combination_label` varchar(500) DEFAULT NULL COMMENT 'Label leg??vel ex: "M / Branca"',
  `sku` varchar(100) DEFAULT NULL,
  `price_override` decimal(10,2) DEFAULT NULL COMMENT 'Pre??o espec??fico da combina????o (NULL = usa pre??o do produto)',
  `stock_quantity` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `product_grade_combinations`
--

INSERT INTO `product_grade_combinations` (`id`, `product_id`, `combination_key`, `combination_label`, `sku`, `price_override`, `stock_quantity`, `is_active`, `created_at`) VALUES
(1, 4, '1:1|2:5', 'Tamanho: P / Cor: Branco', '', NULL, 0, 1, '2026-02-20 19:05:33'),
(2, 4, '1:1|2:6', 'Tamanho: P / Cor: Preto', '', NULL, 0, 1, '2026-02-20 19:05:33'),
(3, 4, '1:1|2:7', 'Tamanho: P / Cor: Amarelo', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(4, 4, '1:2|2:5', 'Tamanho: M / Cor: Branco', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(5, 4, '1:2|2:6', 'Tamanho: M / Cor: Preto', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(6, 4, '1:2|2:7', 'Tamanho: M / Cor: Amarelo', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(7, 4, '1:3|2:5', 'Tamanho: G / Cor: Branco', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(8, 4, '1:3|2:6', 'Tamanho: G / Cor: Preto', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(9, 4, '1:3|2:7', 'Tamanho: G / Cor: Amarelo', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(10, 4, '1:4|2:5', 'Tamanho: GG / Cor: Branco', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(11, 4, '1:4|2:6', 'Tamanho: GG / Cor: Preto', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(12, 4, '1:4|2:7', 'Tamanho: GG / Cor: Amarelo', '', NULL, 0, 1, '2026-02-20 19:05:34'),
(13, 4, '1:8|2:12', 'Tamanho: P / Cor: Branco', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(14, 4, '1:8|2:13', 'Tamanho: P / Cor: Preto', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(15, 4, '1:8|2:14', 'Tamanho: P / Cor: Amarelo', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(16, 4, '1:9|2:12', 'Tamanho: M / Cor: Branco', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(17, 4, '1:9|2:13', 'Tamanho: M / Cor: Preto', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(18, 4, '1:9|2:14', 'Tamanho: M / Cor: Amarelo', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(19, 4, '1:10|2:12', 'Tamanho: G / Cor: Branco', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(20, 4, '1:10|2:13', 'Tamanho: G / Cor: Preto', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(21, 4, '1:10|2:14', 'Tamanho: G / Cor: Amarelo', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(22, 4, '1:11|2:12', 'Tamanho: GG / Cor: Branco', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(23, 4, '1:11|2:13', 'Tamanho: GG / Cor: Preto', '', NULL, 0, 1, '2026-02-23 16:46:59'),
(24, 4, '1:11|2:14', 'Tamanho: GG / Cor: Amarelo', '', NULL, 0, 1, '2026-02-23 16:47:00'),
(25, 4, '1:15|2:19', 'Tamanho: P / Cor: Branco', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(26, 4, '1:15|2:20', 'Tamanho: P / Cor: Preto', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(27, 4, '1:15|2:21', 'Tamanho: P / Cor: Amarelo', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(28, 4, '1:16|2:19', 'Tamanho: M / Cor: Branco', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(29, 4, '1:16|2:20', 'Tamanho: M / Cor: Preto', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(30, 4, '1:16|2:21', 'Tamanho: M / Cor: Amarelo', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(31, 4, '1:17|2:19', 'Tamanho: G / Cor: Branco', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(32, 4, '1:17|2:20', 'Tamanho: G / Cor: Preto', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(33, 4, '1:17|2:21', 'Tamanho: G / Cor: Amarelo', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(34, 4, '1:18|2:19', 'Tamanho: GG / Cor: Branco', NULL, NULL, 0, 1, '2026-02-23 16:48:36'),
(35, 4, '1:18|2:20', 'Tamanho: GG / Cor: Preto', NULL, NULL, 0, 1, '2026-02-23 16:48:37'),
(36, 4, '1:18|2:21', 'Tamanho: GG / Cor: Amarelo', NULL, NULL, 0, 1, '2026-02-23 16:48:37');

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_grade_types`
--

CREATE TABLE `product_grade_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT 'fas fa-th',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `product_grade_types`
--

INSERT INTO `product_grade_types` (`id`, `name`, `description`, `icon`, `created_at`) VALUES
(1, 'Tamanho', 'Varia????es de tamanho do produto (P, M, G, GG, etc.)', 'fas fa-ruler-combined', '2026-02-20 17:56:59'),
(2, 'Cor', 'Varia????es de cor do produto', 'fas fa-palette', '2026-02-20 17:56:59'),
(3, 'Material', 'Tipo de material ou papel utilizado', 'fas fa-layer-group', '2026-02-20 17:56:59'),
(4, 'Acabamento', 'Tipo de acabamento (lamina????o, verniz, etc.)', 'fas fa-magic', '2026-02-20 17:56:59'),
(5, 'Gramatura', 'Gramatura do papel (90g, 150g, 300g, etc.)', 'fas fa-weight-hanging', '2026-02-20 17:56:59'),
(6, 'Formato', 'Formato ou dimens??o do produto', 'fas fa-expand-arrows-alt', '2026-02-20 17:56:59'),
(7, 'Quantidade', 'Faixas de quantidade (100un, 500un, 1000un)', 'fas fa-boxes', '2026-02-20 17:56:59');

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_grade_values`
--

CREATE TABLE `product_grade_values` (
  `id` int(11) NOT NULL,
  `product_grade_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `product_grade_values`
--

INSERT INTO `product_grade_values` (`id`, `product_grade_id`, `value`, `sort_order`, `is_active`, `created_at`) VALUES
(15, 1, 'P', 0, 1, '2026-02-23 16:48:35'),
(16, 1, 'M', 1, 1, '2026-02-23 16:48:35'),
(17, 1, 'G', 2, 1, '2026-02-23 16:48:35'),
(18, 1, 'GG', 3, 1, '2026-02-23 16:48:35'),
(19, 2, 'Branco', 0, 1, '2026-02-23 16:48:35'),
(20, 2, 'Preto', 1, 1, '2026-02-23 16:48:35'),
(21, 2, 'Amarelo', 2, 1, '2026-02-23 16:48:36');

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_images`
--

CREATE TABLE `product_images` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `product_sectors`
--

CREATE TABLE `product_sectors` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `product_sectors`
--

INSERT INTO `product_sectors` (`id`, `product_id`, `sector_id`, `sort_order`) VALUES
(3, 1, 1, 0),
(4, 1, 2, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `stock_items`
--

CREATE TABLE `stock_items` (
  `id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `combination_id` int(11) DEFAULT NULL COMMENT 'NULL = produto sem variação',
  `quantity` decimal(12,2) DEFAULT 0.00,
  `min_quantity` decimal(12,2) DEFAULT 0.00 COMMENT 'Estoque mínimo para alerta',
  `location_code` varchar(50) DEFAULT NULL COMMENT 'Localização física (ex: A1-P3)',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL,
  `stock_item_id` int(11) NOT NULL,
  `warehouse_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `combination_id` int(11) DEFAULT NULL,
  `type` enum('entrada','saida','ajuste','transferencia') NOT NULL DEFAULT 'entrada',
  `quantity` decimal(12,2) NOT NULL,
  `quantity_before` decimal(12,2) DEFAULT 0.00,
  `quantity_after` decimal(12,2) DEFAULT 0.00,
  `reason` varchar(255) DEFAULT NULL COMMENT 'Motivo/observação da movimentação',
  `reference_type` varchar(50) DEFAULT NULL COMMENT 'order, manual, adjustment, transfer',
  `reference_id` int(11) DEFAULT NULL COMMENT 'ID do pedido ou outra referência',
  `destination_warehouse_id` int(11) DEFAULT NULL COMMENT 'Para transferências entre armazéns',
  `user_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subcategories`
--

CREATE TABLE `subcategories` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `subcategories`
--

INSERT INTO `subcategories` (`id`, `category_id`, `name`) VALUES
(1, 1, 'Padrão'),
(3, 3, 'canecas'),
(4, 3, 'canetas'),
(5, 4, 'polo');

-- --------------------------------------------------------

--
-- Estrutura para tabela `subcategory_grades`
--

CREATE TABLE `subcategory_grades` (
  `id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `grade_type_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subcategory_grade_combinations`
--

CREATE TABLE `subcategory_grade_combinations` (
  `id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `combination_key` varchar(255) NOT NULL,
  `combination_label` varchar(500) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subcategory_grade_values`
--

CREATE TABLE `subcategory_grade_values` (
  `id` int(11) NOT NULL,
  `subcategory_grade_id` int(11) NOT NULL,
  `value` varchar(100) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `subcategory_sectors`
--

CREATE TABLE `subcategory_sectors` (
  `id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL,
  `sector_id` int(11) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `system_logs`
--

CREATE TABLE `system_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `system_logs`
--

INSERT INTO `system_logs` (`id`, `user_id`, `action`, `details`, `ip_address`, `created_at`) VALUES
(1, NULL, 'LOGIN_FAIL', 'Failed login attempt for: admin@sistema.com', '::1', '2026-02-11 19:32:06'),
(2, NULL, 'LOGIN_FAIL', 'Failed login attempt for: admin@sistema.com', '::1', '2026-02-11 19:32:21'),
(3, 1, 'LOGIN', 'User logged in: admin@sistema.com', '::1', '2026-02-11 19:36:44'),
(4, 1, 'CREATE_PRODUCT', 'Created product ID: 1 Name: Folha A4', '::1', '2026-02-11 20:15:22'),
(5, 1, 'ORDER_CREATE', 'Pedido #1 criado e inserido no pipeline', '::1', '2026-02-12 11:57:23'),
(6, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: orcamento', '::1', '2026-02-12 11:58:04'),
(7, 1, 'CREATE_USER', 'Created user: juliobenin@yahoo.com.br', '::1', '2026-02-12 12:20:40'),
(8, 1, 'LOGOUT', 'User logged out', '::1', '2026-02-12 12:20:43'),
(9, 2, 'LOGIN', 'User logged in: juliobenin@yahoo.com.br', '::1', '2026-02-12 12:20:54'),
(10, 2, 'LOGOUT', 'User logged out', '::1', '2026-02-12 12:23:01'),
(11, NULL, 'LOGIN_FAIL', 'Failed login attempt for: juliobenin@yahoo.com.br', '::1', '2026-02-12 12:27:43'),
(12, 2, 'LOGIN', 'User logged in: juliobenin@yahoo.com.br', '::1', '2026-02-12 12:27:50'),
(13, 2, 'LOGOUT', 'User logged out', '::1', '2026-02-12 12:28:21'),
(14, NULL, 'LOGIN_FAIL', 'Failed login attempt for: juliobenin@yahoo.com.br', '::1', '2026-02-12 20:45:55'),
(15, NULL, 'LOGIN_FAIL', 'Failed login attempt for: admin@sistema.com', '::1', '2026-02-12 20:46:05'),
(16, 2, 'LOGIN', 'User logged in: juliobenin@yahoo.com.br', '::1', '2026-02-12 20:46:11'),
(17, NULL, 'LOGIN_FAIL', 'Failed login attempt for: admin@sismed.com', '::1', '2026-02-18 10:57:57'),
(18, NULL, 'LOGIN_FAIL', 'Failed login attempt for: juliobenin@yahoo.com.br', '::1', '2026-02-18 10:58:03'),
(19, NULL, 'LOGIN_FAIL', 'Failed login attempt for: juliobenin@yahoo.com.br', '::1', '2026-02-18 10:58:08'),
(20, 2, 'LOGIN', 'User logged in: juliobenin@yahoo.com.br', '::1', '2026-02-18 10:58:13'),
(21, 2, 'LOGOUT', 'User logged out', '::1', '2026-02-18 11:03:21'),
(22, 1, 'LOGIN', 'User logged in: admin@sistema.com', '::1', '2026-02-18 11:03:29'),
(23, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: producao', '::1', '2026-02-18 11:27:16'),
(24, 1, 'CREATE_SECTOR', 'Created sector: Impressão', '::1', '2026-02-18 11:47:09'),
(25, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: orcamento', '::1', '2026-02-18 11:47:51'),
(26, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:18:09'),
(27, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:18:26'),
(28, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:22:24'),
(29, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:25:31'),
(30, 1, 'CATALOG_LINK', 'Link de catálogo gerado para pedido #1', '::1', '2026-02-18 12:28:55'),
(31, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:29:03'),
(32, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:30:31'),
(33, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:30:35'),
(34, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:30:39'),
(35, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:30:47'),
(36, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:31:11'),
(37, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 12:33:04'),
(38, 1, 'CATALOG_LINK', 'Link de catálogo gerado para pedido #1', '::1', '2026-02-18 12:33:11'),
(39, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: venda', '::1', '2026-02-18 12:36:11'),
(40, 1, 'CREATE_SECTOR', 'Created sector: Corte', '::1', '2026-02-18 12:38:48'),
(41, 1, 'UPDATE_SECTOR', 'Updated sector ID: 2', '::1', '2026-02-18 12:39:28'),
(42, 1, 'UPDATE_CATEGORY', 'Updated category ID: 1', '::1', '2026-02-18 13:32:57'),
(43, 1, 'UPDATE_PRODUCT', 'Updated product ID: 1', '::1', '2026-02-18 13:33:31'),
(44, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: producao', '::1', '2026-02-18 13:37:56'),
(45, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #2 -> em_andamento', '::1', '2026-02-18 14:07:49'),
(46, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #2 -> pendente', '::1', '2026-02-18 14:07:53'),
(47, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #1 -> em_andamento', '::1', '2026-02-18 14:07:56'),
(48, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #2 -> em_andamento', '::1', '2026-02-18 14:07:57'),
(49, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #2 -> concluido', '::1', '2026-02-18 14:08:02'),
(50, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #1 -> concluido', '::1', '2026-02-18 14:08:07'),
(51, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #1 -> pendente', '::1', '2026-02-18 14:08:15'),
(52, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #2 -> pendente', '::1', '2026-02-18 14:08:18'),
(53, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #1 -> concluido', '::1', '2026-02-18 14:08:37'),
(54, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 sector #1 -> pendente', '::1', '2026-02-18 14:08:47'),
(55, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 14:27:21'),
(56, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 14:30:34'),
(57, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 14:30:42'),
(58, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 14:30:47'),
(59, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 16:16:20'),
(60, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 16:23:01'),
(61, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 16:23:07'),
(62, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 16:32:00'),
(63, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 16:32:04'),
(64, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #1 sector #1 action:start', 'UNKNOWN', '2026-02-18 16:34:15'),
(65, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 16:40:37'),
(66, 1, 'PIPELINE_UPDATE', 'Updated order details #1', '::1', '2026-02-18 16:40:45'),
(67, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:start', '::1', '2026-02-18 16:45:16'),
(68, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:advance', '::1', '2026-02-18 16:45:23'),
(69, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:advance', '::1', '2026-02-18 16:45:30'),
(70, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-18 17:01:06'),
(71, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-18 17:01:13'),
(72, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:revert', '::1', '2026-02-18 17:01:19'),
(73, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:revert', '::1', '2026-02-18 17:01:24'),
(74, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:start', '::1', '2026-02-18 17:01:28'),
(75, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:advance', '::1', '2026-02-18 17:01:32'),
(76, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:advance', '::1', '2026-02-18 17:01:38'),
(77, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-18 17:01:42'),
(78, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-18 17:01:44'),
(79, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:revert', '::1', '2026-02-18 17:01:51'),
(80, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:advance', '::1', '2026-02-18 17:01:55'),
(81, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:advance', '::1', '2026-02-18 17:02:01'),
(82, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-18 17:06:52'),
(83, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-18 17:06:55'),
(84, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:revert', '::1', '2026-02-18 17:06:59'),
(85, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:revert', '::1', '2026-02-18 17:07:02'),
(86, 1, 'CREATE_PRODUCT', 'Created product ID: 2 Name: caneca', '::1', '2026-02-18 17:08:01'),
(87, 1, 'UPDATE_PRODUCT', 'Updated product ID: 1', '::1', '2026-02-18 17:08:11'),
(88, 1, 'UPDATE_PRODUCT', 'Updated product ID: 2', '::1', '2026-02-18 17:09:05'),
(89, 1, 'CREATE_PRODUCT', 'Created product ID: 3 Name: canetas', '::1', '2026-02-18 17:09:40'),
(90, 1, 'UPDATE_PRODUCT', 'Updated product ID: 2', '::1', '2026-02-18 17:09:55'),
(91, 1, 'DELETE_CATEGORY', 'Deleted category ID: 2', '::1', '2026-02-18 17:10:16'),
(92, 1, 'CREATE_SECTOR', 'Created sector: sublimação', '::1', '2026-02-18 17:10:50'),
(93, 1, 'CREATE_SECTOR', 'Created sector: Acabamentos', '::1', '2026-02-18 17:11:22'),
(94, 1, 'UPDATE_CATEGORY', 'Updated category ID: 3', '::1', '2026-02-18 17:11:35'),
(95, 1, 'ORDER_CREATE', 'Pedido #2 criado na etapa Orcamento', '::1', '2026-02-18 17:12:07'),
(96, 1, 'PIPELINE_MOVE', 'Order #2 moved to stage: venda', '::1', '2026-02-18 17:13:03'),
(97, 1, 'PIPELINE_MOVE', 'Order #2 moved to stage: producao', '::1', '2026-02-18 17:13:08'),
(98, 1, 'PIPELINE_MOVE', 'Order #2 moved to stage: venda', '::1', '2026-02-18 17:14:29'),
(99, 1, 'PIPELINE_MOVE', 'Order #2 moved to stage: producao', '::1', '2026-02-18 17:15:12'),
(100, 1, 'PIPELINE_MOVE', 'Order #2 moved to stage: venda', '::1', '2026-02-18 17:29:45'),
(101, 1, 'PIPELINE_UPDATE', 'Updated order details #2', '::1', '2026-02-18 17:30:21'),
(102, 1, 'PIPELINE_MOVE', 'Order #2 moved to stage: producao', '::1', '2026-02-18 17:30:27'),
(103, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #2 item #3 sector #1 action:start', '::1', '2026-02-18 17:30:47'),
(104, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #2 item #4 sector #1 action:start', '::1', '2026-02-18 17:31:00'),
(105, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #2 item #3 sector #1 action:advance', '::1', '2026-02-18 17:31:12'),
(106, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #2 item #4 sector #1 action:advance', '::1', '2026-02-18 17:31:19'),
(107, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:start', '::1', '2026-02-18 17:31:22'),
(108, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #2 item #4 sector #3 action:revert', '::1', '2026-02-18 17:31:34'),
(109, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #2 item #4 sector #3 action:start', '::1', '2026-02-18 17:34:25'),
(110, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #2 item #4 sector #3 action:advance', '::1', '2026-02-18 17:34:28'),
(111, 1, 'CREATE_SECTOR', 'Created sector: Desenho', '::1', '2026-02-18 17:37:08'),
(112, 1, 'UPDATE_CATEGORY', 'Updated category ID: 3', '::1', '2026-02-18 17:37:34'),
(113, 1, 'PIPELINE_MOVE', 'Order #2 moved to stage: concluido', '::1', '2026-02-18 18:02:14'),
(114, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:advance', '::1', '2026-02-18 19:00:51'),
(115, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-18 19:00:58'),
(116, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #1 action:advance', '::1', '2026-02-18 19:01:03'),
(117, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:advance', '::1', '2026-02-18 19:01:07'),
(118, 1, 'ORDER_CREATE', 'Pedido #3 criado na etapa Orcamento', '::1', '2026-02-18 19:01:55'),
(119, 1, 'PIPELINE_MOVE', 'Order #3 moved to stage: venda', '::1', '2026-02-18 19:02:07'),
(120, 1, 'PIPELINE_UPDATE', 'Updated order details #3', '::1', '2026-02-18 19:02:45'),
(121, 1, 'PIPELINE_UPDATE', 'Updated order details #3', '::1', '2026-02-18 19:02:52'),
(122, 1, 'PIPELINE_MOVE', 'Order #3 moved to stage: producao', '::1', '2026-02-18 19:03:00'),
(123, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #3 item #6 sector #5 action:advance', '::1', '2026-02-18 19:06:50'),
(124, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #3 item #6 sector #1 action:revert', '::1', '2026-02-18 19:08:56'),
(125, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #3 item #6 sector #5 action:advance', '::1', '2026-02-18 19:10:16'),
(126, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #3 item #6 sector #1 action:revert', '::1', '2026-02-18 19:10:25'),
(127, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: venda', '::1', '2026-02-18 19:15:31'),
(128, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: producao', '::1', '2026-02-18 19:16:04'),
(129, 1, 'ITEM_LOG_ADDED', 'Log #1 added to order #3 item #5', '::1', '2026-02-18 19:16:43'),
(130, 1, 'ITEM_LOG_ADDED', 'Log #2 added to order #3 item #6', '::1', '2026-02-18 19:17:03'),
(131, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: venda', '::1', '2026-02-18 19:17:52'),
(132, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: orcamento', '::1', '2026-02-18 19:18:03'),
(133, 1, 'ORDER_CREATE', 'Pedido #4 criado na etapa Orcamento', '::1', '2026-02-18 19:28:09'),
(134, 1, 'PIPELINE_MOVE', 'Order #4 moved to stage: contato', '::1', '2026-02-18 19:28:33'),
(135, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: venda', '::1', '2026-02-18 19:47:14'),
(136, 1, 'ORDER_CREATE', 'Pedido #5 criado na etapa Orcamento', '::1', '2026-02-18 19:53:17'),
(137, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #3 item #6 sector #5 action:advance', '::1', '2026-02-19 17:53:25'),
(138, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: preparacao', '::1', '2026-02-19 18:45:22'),
(139, 1, 'PREPARATION_TOGGLE', 'Preparation \'revisao_arquivos\' checked for order #1', '::1', '2026-02-19 19:23:25'),
(140, 1, 'PREPARATION_TOGGLE', 'Preparation \'corte_acabamento\' checked for order #1', '::1', '2026-02-19 19:23:30'),
(141, 1, 'PREPARATION_TOGGLE', 'Preparation \'embalagem\' checked for order #1', '::1', '2026-02-19 19:23:33'),
(142, 1, 'PREPARATION_TOGGLE', 'Preparation \'conferencia_qtd\' checked for order #1', '::1', '2026-02-19 19:23:35'),
(143, 1, 'PREPARATION_TOGGLE', 'Preparation \'conferencia_qual\' checked for order #1', '::1', '2026-02-19 19:23:37'),
(144, 1, 'PREPARATION_TOGGLE', 'Preparation \'pronto_envio\' checked for order #1', '::1', '2026-02-19 19:23:40'),
(145, 1, 'PREPARATION_TOGGLE', 'Preparation \'pronto_envio\' unchecked for order #1', '::1', '2026-02-19 19:24:13'),
(146, 1, 'ORDER_CREATE', 'Pedido #6 criado na etapa Contato', '::1', '2026-02-19 19:58:03'),
(147, 1, 'PIPELINE_MOVE', 'Order #6 moved to stage: orcamento', '::1', '2026-02-19 19:58:22'),
(148, 1, 'CATALOG_LINK', 'Link de catálogo gerado para pedido #6', '::1', '2026-02-19 19:58:38'),
(149, 1, 'PIPELINE_UPDATE', 'Updated order details #6', '::1', '2026-02-19 19:59:14'),
(150, 1, 'PIPELINE_UPDATE', 'Updated order details #6', '::1', '2026-02-19 19:59:29'),
(151, 1, 'PIPELINE_MOVE', 'Order #6 moved to stage: venda', '::1', '2026-02-19 19:59:35'),
(152, 1, 'PIPELINE_MOVE', 'Order #6 moved to stage: producao', '::1', '2026-02-19 19:59:45'),
(153, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #6 item #9 sector #5 action:advance', '::1', '2026-02-19 19:59:59'),
(154, 1, 'PREPARATION_TOGGLE', 'Preparation \'pronto_envio\' checked for order #1', '::1', '2026-02-19 20:24:45'),
(155, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:revert', '::1', '2026-02-20 10:36:18'),
(156, 1, 'PRODUCTION_SECTOR_MOVE', 'Order #1 item #2 sector #2 action:advance', '::1', '2026-02-20 10:36:39'),
(157, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: envio', '::1', '2026-02-20 10:37:46'),
(158, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: financeiro', '::1', '2026-02-20 13:38:24'),
(159, 1, 'SETTINGS_UPDATE', 'Configurações bancárias/boleto atualizadas', '::1', '2026-02-20 17:27:02'),
(160, 1, 'PIPELINE_MOVE', 'Order #4 moved to stage: cancelado', '::1', '2026-02-20 17:33:20'),
(161, 1, 'PIPELINE_MOVE', 'Order #5 moved to stage: cancelado', '::1', '2026-02-20 17:33:24'),
(162, 1, 'PIPELINE_MOVE', 'Order #3 moved to stage: cancelado', '::1', '2026-02-20 17:33:29'),
(163, 1, 'PIPELINE_MOVE', 'Order #6 moved to stage: cancelado', '::1', '2026-02-20 17:33:33'),
(164, 1, 'PIPELINE_MOVE', 'Order #1 moved to stage: cancelado', '::1', '2026-02-20 17:33:37'),
(165, 1, 'CREATE_CATEGORY', 'Created category: roupas', '::1', '2026-02-20 19:04:17'),
(166, 1, 'CREATE_PRODUCT', 'Created product ID: 4 Name: camisa polo', '::1', '2026-02-20 19:05:32'),
(167, 1, 'ORDER_CREATE', 'Pedido #7 criado na etapa Orcamento', '::1', '2026-02-23 11:20:54'),
(168, 1, 'CATALOG_LINK', 'Link de catálogo gerado para pedido #7', '::1', '2026-02-23 11:22:28'),
(169, 1, 'CATALOG_LINK', 'Link de catálogo gerado para pedido #7', '::1', '2026-02-23 11:59:25'),
(170, 1, 'PIPELINE_MOVE', 'Order #7 moved to stage: venda', '::1', '2026-02-23 12:04:26'),
(171, 1, 'PIPELINE_MOVE', 'Order #7 moved to stage: producao', '::1', '2026-02-23 12:04:31'),
(172, 1, 'PIPELINE_MOVE', 'Order #7 moved to stage: preparacao', '::1', '2026-02-23 14:29:48'),
(173, 1, 'UPDATE_PRODUCT', 'Updated product ID: 4', '::1', '2026-02-23 16:46:57'),
(174, 1, 'PIPELINE_MOVE', 'Order #7 moved to stage: producao', '::1', '2026-02-23 16:47:16'),
(175, 1, 'PIPELINE_MOVE', 'Order #7 moved to stage: venda', '::1', '2026-02-23 16:47:39'),
(176, 1, 'PIPELINE_MOVE', 'Order #7 moved to stage: orcamento', '::1', '2026-02-23 16:47:51'),
(177, 1, 'CATALOG_LINK', 'Link de catálogo gerado para pedido #7', '::1', '2026-02-23 16:47:57'),
(178, 1, 'UPDATE_PRODUCT', 'Updated product ID: 4', '::1', '2026-02-23 16:48:34'),
(179, 1, 'LOGIN', 'User logged in: admin@sistema.com', '::1', '2026-02-26 11:45:56'),
(180, 1, 'SETTINGS_UPDATE', 'Configurações da empresa atualizadas', '::1', '2026-02-26 12:34:07'),
(181, 1, 'PIPELINE_MOVE', 'Order #7 moved to stage: venda', '::1', '2026-02-26 13:54:39'),
(182, NULL, 'LOGIN_FAIL', 'Failed login attempt for: admin@sistema.com', '127.0.0.1', '2026-02-26 14:23:01'),
(183, 1, 'LOGOUT', 'User logged out', '::1', '2026-02-26 14:23:09'),
(184, NULL, 'LOGIN_FAIL', 'Failed login attempt for: admin@admin.com', '127.0.0.1', '2026-02-26 16:33:39'),
(185, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:34:27'),
(186, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:35:00'),
(187, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:35:03'),
(188, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:35:23'),
(189, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:35:31'),
(190, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:39:39'),
(191, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:39:42'),
(192, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:40:28'),
(193, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:40:31'),
(194, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:40:52'),
(195, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:40:55'),
(196, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:41:59'),
(197, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:42:03'),
(198, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:43:04'),
(199, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:44:15'),
(200, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:44:37'),
(201, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 16:48:29'),
(202, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 16:48:32'),
(203, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 17:02:53'),
(204, 1, 'LOGOUT', 'User logged out', '127.0.0.1', '2026-02-26 17:36:40'),
(205, 1, 'LOGIN', 'User logged in: admin@sistema.com', '127.0.0.1', '2026-02-26 17:36:50');

-- --------------------------------------------------------

--
-- Estrutura para tabela `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','funcionario') DEFAULT 'funcionario',
  `group_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `group_id`, `created_at`) VALUES
(1, 'Administrador', 'admin@sistema.com', '$2y$10$PuVBZ5YUJwQZVH4R63IjNOIkod63vRF2cRfUJhEdpx/uLJBgdFVVK', 'admin', 1, '2026-02-11 19:04:32'),
(2, 'julio cesar benin kronhardt', 'juliobenin@yahoo.com.br', '$2y$10$Gveko.qt38LeL5anrU5XruO8qjfmHTjZyyhAnObHE3IXWYy2jWKRe', 'funcionario', 7, '2026-02-12 12:20:39');

-- --------------------------------------------------------

--
-- Estrutura para tabela `user_groups`
--

CREATE TABLE `user_groups` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `user_groups`
--

INSERT INTO `user_groups` (`id`, `name`, `description`) VALUES
(1, 'Administradores', 'Acesso total ao sistema'),
(7, 'Produção', 'acesso as etapas de produção de um pedido');

-- --------------------------------------------------------

--
-- Estrutura para tabela `warehouses`
--

CREATE TABLE `warehouses` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `address` varchar(500) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(2) DEFAULT NULL,
  `zip_code` varchar(10) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `warehouses`
--

INSERT INTO `warehouses` (`id`, `name`, `address`, `city`, `state`, `zip_code`, `phone`, `notes`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Estoque Principal', 'Endereço da sede', NULL, NULL, NULL, NULL, 'Armazém principal da empresa', 1, '2026-02-20 17:17:33', '2026-02-20 17:17:33');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `catalog_links`
--
ALTER TABLE `catalog_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `order_id` (`order_id`);

--
-- Índices de tabela `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Índices de tabela `category_grades`
--
ALTER TABLE `category_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_category_grade` (`category_id`,`grade_type_id`),
  ADD KEY `grade_type_id` (`grade_type_id`);

--
-- Índices de tabela `category_grade_combinations`
--
ALTER TABLE `category_grade_combinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_category_combination` (`category_id`,`combination_key`);

--
-- Índices de tabela `category_grade_values`
--
ALTER TABLE `category_grade_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_grade_id` (`category_grade_id`);

--
-- Índices de tabela `category_sectors`
--
ALTER TABLE `category_sectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_cat_sector` (`category_id`,`sector_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Índices de tabela `company_settings`
--
ALTER TABLE `company_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`);

--
-- Índices de tabela `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `price_table_id` (`price_table_id`);

--
-- Índices de tabela `group_permissions`
--
ALTER TABLE `group_permissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group_id` (`group_id`);

--
-- Índices de tabela `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Índices de tabela `order_extra_costs`
--
ALTER TABLE `order_extra_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Índices de tabela `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `order_item_logs`
--
ALTER TABLE `order_item_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_order_id` (`order_id`),
  ADD KEY `idx_order_item_id` (`order_item_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Índices de tabela `order_preparation_checklist`
--
ALTER TABLE `order_preparation_checklist`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_order_key` (`order_id`,`check_key`),
  ADD KEY `idx_order_id` (`order_id`);

--
-- Índices de tabela `order_production_sectors`
--
ALTER TABLE `order_production_sectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_item_sector` (`order_item_id`,`sector_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Índices de tabela `pipeline_history`
--
ALTER TABLE `pipeline_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `changed_by` (`changed_by`);

--
-- Índices de tabela `pipeline_stage_goals`
--
ALTER TABLE `pipeline_stage_goals`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stage` (`stage`);

--
-- Índices de tabela `preparation_steps`
--
ALTER TABLE `preparation_steps`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `step_key` (`step_key`);

--
-- Índices de tabela `price_tables`
--
ALTER TABLE `price_tables`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `price_table_items`
--
ALTER TABLE `price_table_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_table_product` (`price_table_id`,`product_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `production_sectors`
--
ALTER TABLE `production_sectors`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_product_category` (`category_id`),
  ADD KEY `fk_product_subcategory` (`subcategory_id`);

--
-- Índices de tabela `product_grades`
--
ALTER TABLE `product_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_grade` (`product_id`,`grade_type_id`),
  ADD KEY `grade_type_id` (`grade_type_id`);

--
-- Índices de tabela `product_grade_combinations`
--
ALTER TABLE `product_grade_combinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_combination` (`product_id`,`combination_key`);

--
-- Índices de tabela `product_grade_types`
--
ALTER TABLE `product_grade_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Índices de tabela `product_grade_values`
--
ALTER TABLE `product_grade_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_grade_id` (`product_grade_id`);

--
-- Índices de tabela `product_images`
--
ALTER TABLE `product_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_id` (`product_id`);

--
-- Índices de tabela `product_sectors`
--
ALTER TABLE `product_sectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_product_sector` (`product_id`,`sector_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Índices de tabela `stock_items`
--
ALTER TABLE `stock_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_warehouse_product_combo` (`warehouse_id`,`product_id`,`combination_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `combination_id` (`combination_id`);

--
-- Índices de tabela `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_item_id` (`stock_item_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `idx_stock_mov_product` (`product_id`),
  ADD KEY `idx_stock_mov_warehouse` (`warehouse_id`),
  ADD KEY `idx_stock_mov_created` (`created_at`),
  ADD KEY `idx_stock_mov_type` (`type`);

--
-- Índices de tabela `subcategories`
--
ALTER TABLE `subcategories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `category_id` (`category_id`);

--
-- Índices de tabela `subcategory_grades`
--
ALTER TABLE `subcategory_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subcategory_grade` (`subcategory_id`,`grade_type_id`),
  ADD KEY `grade_type_id` (`grade_type_id`);

--
-- Índices de tabela `subcategory_grade_combinations`
--
ALTER TABLE `subcategory_grade_combinations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_subcategory_combination` (`subcategory_id`,`combination_key`);

--
-- Índices de tabela `subcategory_grade_values`
--
ALTER TABLE `subcategory_grade_values`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subcategory_grade_id` (`subcategory_grade_id`);

--
-- Índices de tabela `subcategory_sectors`
--
ALTER TABLE `subcategory_sectors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_sub_sector` (`subcategory_id`,`sector_id`),
  ADD KEY `sector_id` (`sector_id`);

--
-- Índices de tabela `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices de tabela `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_user_group` (`group_id`);

--
-- Índices de tabela `user_groups`
--
ALTER TABLE `user_groups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Índices de tabela `warehouses`
--
ALTER TABLE `warehouses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `catalog_links`
--
ALTER TABLE `catalog_links`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `category_grades`
--
ALTER TABLE `category_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `category_grade_combinations`
--
ALTER TABLE `category_grade_combinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de tabela `category_grade_values`
--
ALTER TABLE `category_grade_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `category_sectors`
--
ALTER TABLE `category_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de tabela `company_settings`
--
ALTER TABLE `company_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=70;

--
-- AUTO_INCREMENT de tabela `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `group_permissions`
--
ALTER TABLE `group_permissions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `order_extra_costs`
--
ALTER TABLE `order_extra_costs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `order_item_logs`
--
ALTER TABLE `order_item_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `order_preparation_checklist`
--
ALTER TABLE `order_preparation_checklist`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `order_production_sectors`
--
ALTER TABLE `order_production_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de tabela `pipeline_history`
--
ALTER TABLE `pipeline_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `pipeline_stage_goals`
--
ALTER TABLE `pipeline_stage_goals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de tabela `preparation_steps`
--
ALTER TABLE `preparation_steps`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `price_tables`
--
ALTER TABLE `price_tables`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `price_table_items`
--
ALTER TABLE `price_table_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `production_sectors`
--
ALTER TABLE `production_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `product_grades`
--
ALTER TABLE `product_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `product_grade_combinations`
--
ALTER TABLE `product_grade_combinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT de tabela `product_grade_types`
--
ALTER TABLE `product_grade_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `product_grade_values`
--
ALTER TABLE `product_grade_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `product_images`
--
ALTER TABLE `product_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `product_sectors`
--
ALTER TABLE `product_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `stock_items`
--
ALTER TABLE `stock_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subcategories`
--
ALTER TABLE `subcategories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `subcategory_grades`
--
ALTER TABLE `subcategory_grades`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subcategory_grade_combinations`
--
ALTER TABLE `subcategory_grade_combinations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subcategory_grade_values`
--
ALTER TABLE `subcategory_grade_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `subcategory_sectors`
--
ALTER TABLE `subcategory_sectors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=206;

--
-- AUTO_INCREMENT de tabela `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `user_groups`
--
ALTER TABLE `user_groups`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `warehouses`
--
ALTER TABLE `warehouses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `catalog_links`
--
ALTER TABLE `catalog_links`
  ADD CONSTRAINT `catalog_links_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `category_grades`
--
ALTER TABLE `category_grades`
  ADD CONSTRAINT `category_grades_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_grades_ibfk_2` FOREIGN KEY (`grade_type_id`) REFERENCES `product_grade_types` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `category_grade_combinations`
--
ALTER TABLE `category_grade_combinations`
  ADD CONSTRAINT `category_grade_combinations_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `category_grade_values`
--
ALTER TABLE `category_grade_values`
  ADD CONSTRAINT `category_grade_values_ibfk_1` FOREIGN KEY (`category_grade_id`) REFERENCES `category_grades` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `category_sectors`
--
ALTER TABLE `category_sectors`
  ADD CONSTRAINT `category_sectors_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `category_sectors_ibfk_2` FOREIGN KEY (`sector_id`) REFERENCES `production_sectors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_ibfk_1` FOREIGN KEY (`price_table_id`) REFERENCES `price_tables` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `group_permissions`
--
ALTER TABLE `group_permissions`
  ADD CONSTRAINT `group_permissions_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Restrições para tabelas `order_extra_costs`
--
ALTER TABLE `order_extra_costs`
  ADD CONSTRAINT `order_extra_costs_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Restrições para tabelas `order_preparation_checklist`
--
ALTER TABLE `order_preparation_checklist`
  ADD CONSTRAINT `order_preparation_checklist_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `order_production_sectors`
--
ALTER TABLE `order_production_sectors`
  ADD CONSTRAINT `order_production_sectors_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_production_sectors_ibfk_2` FOREIGN KEY (`order_item_id`) REFERENCES `order_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_production_sectors_ibfk_3` FOREIGN KEY (`sector_id`) REFERENCES `production_sectors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `pipeline_history`
--
ALTER TABLE `pipeline_history`
  ADD CONSTRAINT `pipeline_history_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pipeline_history_ibfk_2` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `price_table_items`
--
ALTER TABLE `price_table_items`
  ADD CONSTRAINT `price_table_items_ibfk_1` FOREIGN KEY (`price_table_id`) REFERENCES `price_tables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `price_table_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_product_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_product_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `product_grades`
--
ALTER TABLE `product_grades`
  ADD CONSTRAINT `product_grades_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_grades_ibfk_2` FOREIGN KEY (`grade_type_id`) REFERENCES `product_grade_types` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `product_grade_combinations`
--
ALTER TABLE `product_grade_combinations`
  ADD CONSTRAINT `product_grade_combinations_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `product_grade_values`
--
ALTER TABLE `product_grade_values`
  ADD CONSTRAINT `product_grade_values_ibfk_1` FOREIGN KEY (`product_grade_id`) REFERENCES `product_grades` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `product_images`
--
ALTER TABLE `product_images`
  ADD CONSTRAINT `product_images_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `product_sectors`
--
ALTER TABLE `product_sectors`
  ADD CONSTRAINT `product_sectors_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `product_sectors_ibfk_2` FOREIGN KEY (`sector_id`) REFERENCES `production_sectors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `stock_items`
--
ALTER TABLE `stock_items`
  ADD CONSTRAINT `stock_items_ibfk_1` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_items_ibfk_3` FOREIGN KEY (`combination_id`) REFERENCES `product_grade_combinations` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`stock_item_id`) REFERENCES `stock_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`warehouse_id`) REFERENCES `warehouses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `stock_movements_ibfk_4` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `subcategories`
--
ALTER TABLE `subcategories`
  ADD CONSTRAINT `subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subcategory_grades`
--
ALTER TABLE `subcategory_grades`
  ADD CONSTRAINT `subcategory_grades_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subcategory_grades_ibfk_2` FOREIGN KEY (`grade_type_id`) REFERENCES `product_grade_types` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subcategory_grade_combinations`
--
ALTER TABLE `subcategory_grade_combinations`
  ADD CONSTRAINT `subcategory_grade_combinations_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subcategory_grade_values`
--
ALTER TABLE `subcategory_grade_values`
  ADD CONSTRAINT `subcategory_grade_values_ibfk_1` FOREIGN KEY (`subcategory_grade_id`) REFERENCES `subcategory_grades` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `subcategory_sectors`
--
ALTER TABLE `subcategory_sectors`
  ADD CONSTRAINT `subcategory_sectors_ibfk_1` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subcategory_sectors_ibfk_2` FOREIGN KEY (`sector_id`) REFERENCES `production_sectors` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `system_logs`
--
ALTER TABLE `system_logs`
  ADD CONSTRAINT `system_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_group` FOREIGN KEY (`group_id`) REFERENCES `user_groups` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
