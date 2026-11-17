-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 08/11/2026 às 17:09
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
-- Banco de dados: `mobaccess`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `estatisticas_mobilidade`
--

CREATE TABLE `estatisticas_mobilidade` (
  `id_local` int(11) NOT NULL,
  `nome_local` varchar(255) NOT NULL,
  `dia_semana` int(11) NOT NULL,
  `is_feriado` tinyint(1) DEFAULT 0,
  `distancia_metro_m` int(11) NOT NULL,
  `is_polo_saude` tinyint(1) DEFAULT 0,
  `usuarios_historico_dia` int(11) NOT NULL,
  `demanda_real` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `estatisticas_mobilidade`
--

INSERT INTO `estatisticas_mobilidade` (`id_local`, `nome_local`, `dia_semana`, `is_feriado`, `distancia_metro_m`, `is_polo_saude`, `usuarios_historico_dia`, `demanda_real`) VALUES
(1, 'Estação da Luz', 1, 0, 50, 0, 150, 25),
(2, 'Estação da Luz', 2, 0, 50, 0, 180, 28),
(3, 'Hospital das Clínicas', 1, 0, 120, 1, 200, 35),
(4, 'Faculdade Z', 3, 0, 450, 0, 90, 12),
(5, 'Terminal Sacomã', 0, 1, 20, 0, 40, 5),
(6, 'Centro de Reabilitação', 4, 0, 200, 1, 130, 22);

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs_geolocalizacao`
--

CREATE TABLE `logs_geolocalizacao` (
  `id` bigint(20) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `reserva_id` int(11) DEFAULT NULL,
  `scooter_id` int(11) DEFAULT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `precisao_m` float DEFAULT NULL,
  `origem` enum('usuario','scooter') NOT NULL DEFAULT 'usuario',
  `criado_em` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `medicoes_fluxo`
--

CREATE TABLE `medicoes_fluxo` (
  `id` bigint(20) NOT NULL,
  `zona` varchar(40) NOT NULL,
  `janela_inicio` datetime NOT NULL,
  `reservas_iniciadas` int(11) NOT NULL DEFAULT 0,
  `devolucoes` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `reservas`
--

CREATE TABLE `reservas` (
  `id` int(11) NOT NULL,
  `scooter_id` int(11) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `data_reserva` datetime DEFAULT current_timestamp(),
  `data_entrega` datetime DEFAULT current_timestamp(),
  `status` varchar(20) NOT NULL,
  `horas` int(11) DEFAULT 1,
  `valor_total` decimal(8,2) DEFAULT 0.00,
  `status_pagamento` varchar(20) DEFAULT 'pendente',
  `metodo_pagamento` varchar(20) DEFAULT NULL,
  `liberada` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `reservas`
--

INSERT INTO `reservas` (`id`, `scooter_id`, `usuario`, `usuario_id`, `data_reserva`, `data_entrega`, `status`, `horas`, `valor_total`, `status_pagamento`, `metodo_pagamento`, `liberada`) VALUES
(9, 1, 'Cristiano', 7, '2026-07-17 20:05:56', '2026-07-17 20:06:46', '', 1, 5.00, 'aprovado', 'pix', 0),
(10, 1, 'Cristiano', 7, '2026-10-11 16:26:33', '2026-10-11 16:27:07', '', 1, 5.00, 'aprovado', 'debito', 0),
(11, 1, 'Cristiano', 7, '2026-10-11 21:08:29', '2026-10-11 21:10:15', '', 1, 5.00, 'aprovado', 'debito', 0),
(12, 1, 'Manuelle Martins Macedo', 8, '2026-10-11 23:45:12', '2026-10-11 23:45:22', '', 1, 5.00, 'aprovado', 'debito', 0),
(13, 3, 'João Silva', 14, '2026-10-12 00:32:37', '2026-10-12 00:34:34', '', 2, 20.00, 'aprovado', 'credito', 0),
(14, 1, 'Cristiano', 7, '2026-10-18 18:08:37', '2026-10-18 18:09:06', '', 1, 5.00, 'aprovado', 'debito', 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `scooters`
--

CREATE TABLE `scooters` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `disponivel` tinyint(1) DEFAULT 1,
  `qr_code` varchar(50) DEFAULT NULL,
  `tipo` varchar(50) DEFAULT 'Padrão',
  `preco_hora` decimal(6,2) DEFAULT 5.00,
  `codigo_qr` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `scooters`
--

INSERT INTO `scooters` (`id`, `nome`, `latitude`, `longitude`, `disponivel`, `qr_code`, `tipo`, `preco_hora`, `codigo_qr`) VALUES
(1, 'Scooter 01', -23.55100000, -46.63200000, 1, 'SCOOTER01', 'City', 5.00, 'MOBACCESS-01'),
(2, 'Scooter 02', -23.55300000, -46.63500000, 1, 'SCOOTER02', 'Pro', 8.00, 'MOBACCESS-02'),
(3, 'Scooter 03', -23.54800000, -46.63000000, 1, 'SCOOTER03', 'Max', 10.00, 'MOBACCESS-03');

-- --------------------------------------------------------

--
-- Estrutura para tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `email` varchar(50) NOT NULL,
  `cpf` varchar(20) NOT NULL,
  `telefone` varchar(20) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `criado_em` timestamp NOT NULL DEFAULT current_timestamp(),
  `consentimento_geo` tinyint(1) DEFAULT NULL,
  `consentimento_geo_em` datetime DEFAULT NULL,
  `perfil` enum('usuario','admin') NOT NULL DEFAULT 'usuario'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `usuarios`
--

INSERT INTO `usuarios` (`id`, `nome`, `email`, `cpf`, `telefone`, `senha`, `criado_em`, `consentimento_geo`, `consentimento_geo_em`, `perfil`) VALUES
(6, 'Cristiano Aparecido de Macedo', 'cristianoaparecido.abelhinha@gmail.com', '', '', '$2y$10$smg66GrZf6isR', '2026-07-09 23:38:43', NULL, NULL, 'usuario'),
(7, 'Cristiano', 'cseletrotecnica@gmail.com', '', '', '$2y$10$Bw1o29sUQMS8a6XDLIUEG.ezUNjdEhYSfjX7wPuWFlAbrljLWesI2', '2026-07-09 23:48:16', 1, '2026-10-11 21:00:15', 'admin'),
(8, 'Manuelle Martins Macedo', 'manuzinha@gmail.com', '', '', '$2y$10$rTmKaVpTHYWCK4VBKfEW7OhmJJzngd7Sx7jcktr0YYZOC6E.RKBau', '2026-10-12 02:44:33', 1, '2026-10-11 23:44:45', 'usuario'),
(9, 'Cristiano', 'cris@gmail.com', '', '', '$2y$10$MLrBY1y/6c5RCQpwDOzWHeLdW0StXM8R6DVrn6GKtr5Ra6q5YiNsS', '2026-10-12 02:47:34', 1, '2026-10-12 00:06:07', 'usuario'),
(10, 'Manuelle Macedo', 'manuelle@hotmail.com', '', '', '$2y$10$KXyRpGkmFKdn.p6cosZxb.IjNccF3A97xaF2Q1dv8L5mugkMT7VRy', '2026-10-12 03:12:57', 1, '2026-10-12 00:13:14', 'usuario'),
(11, 'Jorge', 'jorginho@gmail.com', '', '', '$2y$10$3Rk1nKLMj2YKOnGlxp5DS.1yr3VigKF7s21yjhUx/fuuaORP1.aGa', '2026-10-12 03:16:02', 1, '2026-10-12 00:16:19', 'usuario'),
(12, 'Fernando Cândido', 'mofe@gmail.com', '', '', '$2y$10$IvzTWANHvf6xDuZrLDTEhuQGK7FGQ2m0i0r7r09DOj56wg/wNX2Cm', '2026-10-12 03:23:22', 0, '2026-10-12 00:24:00', 'usuario'),
(13, 'Adriana Macedo', 'dri@hotmail.com', '', '', '$2y$10$EHLBxvMPB0VKPeIpVXLleOc0W7HhImgCXBYJ2fJ.fGxwX67MG9GMa', '2026-10-12 03:25:59', 0, '2026-10-12 00:27:47', 'usuario'),
(14, 'João Silva', 'joao@gmail.com', '', '', '$2y$10$vPDZXXLcw6yTAbWfF79/IeZtDTM.7ZDPWHaE9GKoqYmpONsrv7H.a', '2026-10-12 03:31:02', 0, '2026-10-12 00:31:16', 'usuario'),
(15, 'teste', 'teste@gmail.com', '', '', '$2y$10$3U7NcGnMJsNpiq8nKvfyB.RMw3y64DpI53D3SVWwoh47VHbaU.zCq', '2026-11-05 14:11:26', NULL, NULL, 'usuario');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `estatisticas_mobilidade`
--
ALTER TABLE `estatisticas_mobilidade`
  ADD PRIMARY KEY (`id_local`);

--
-- Índices de tabela `logs_geolocalizacao`
--
ALTER TABLE `logs_geolocalizacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_geo_criado` (`criado_em`),
  ADD KEY `idx_geo_reserva` (`reserva_id`),
  ADD KEY `idx_geo_usuario` (`usuario_id`);

--
-- Índices de tabela `medicoes_fluxo`
--
ALTER TABLE `medicoes_fluxo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_zona_janela` (`zona`,`janela_inicio`);

--
-- Índices de tabela `reservas`
--
ALTER TABLE `reservas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scooter_id` (`scooter_id`),
  ADD KEY `idx_reservas_usuario_id` (`usuario_id`),
  ADD KEY `idx_reservas_data` (`data_reserva`);

--
-- Índices de tabela `scooters`
--
ALTER TABLE `scooters`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `estatisticas_mobilidade`
--
ALTER TABLE `estatisticas_mobilidade`
  MODIFY `id_local` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `logs_geolocalizacao`
--
ALTER TABLE `logs_geolocalizacao`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `medicoes_fluxo`
--
ALTER TABLE `medicoes_fluxo`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `reservas`
--
ALTER TABLE `reservas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de tabela `scooters`
--
ALTER TABLE `scooters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `reservas`
--
ALTER TABLE `reservas`
  ADD CONSTRAINT `reservas_ibfk_1` FOREIGN KEY (`scooter_id`) REFERENCES `scooters` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
