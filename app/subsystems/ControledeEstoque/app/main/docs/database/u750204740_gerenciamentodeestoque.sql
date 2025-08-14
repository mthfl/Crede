-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 11/06/2025 às 02:32
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
-- Banco de dados: `u750204740_gerenciamentodeestoque`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `movimentacao`
--

CREATE TABLE `movimentacao` (
  `id` int(11) NOT NULL,
  `fk_produtos_id` int(11) DEFAULT NULL,
  `fk_responsaveis_id` int(11) DEFAULT NULL,
  `datareg` date NOT NULL,
  `barcode_produto` char(100) DEFAULT NULL,
  `quantidade_retirada` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `movimentacao`
--

INSERT INTO `movimentacao` (`id`, `fk_produtos_id`, `fk_responsaveis_id`, `datareg`, `barcode_produto`, `quantidade_retirada`) VALUES
(1, 1, 1, '2025-04-20', '742832695084', '2'),
(2, 2, 1, '2025-05-29', '7506306233362', '5'),
(3, 18, 1, '2025-05-29', '1245555', '10'),
(4, 3, 1, '2025-05-30', '6195900', '5');

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `barcode` char(100) DEFAULT NULL,
  `nome_produto` varchar(100) DEFAULT NULL,
  `quantidade` decimal(10,0) DEFAULT NULL,
  `natureza` enum('limpeza','expedientes','manutencao','eletrico','hidraulico','educacao_fisica','epi','copa_e_cozinha','informatica','ferramentas') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `produtos`
--

INSERT INTO `produtos` (`id`, `barcode`, `nome_produto`, `quantidade`, `natureza`) VALUES
(1, '742832695084', 'Máscara de Cirurgica', 20, 'limpeza'),
(2, '7506306233362', 'desodorante AXE', 15, 'limpeza'),
(3, '6195900', 'maquininha', 20, 'eletrico'),
(10, '14666525', 'fones de ouvido', 10, 'eletrico'),
(13, '5752222', 'copo', 10, 'copa_e_cozinha'),
(17, '865590120', 'blaze', 1, 'manutencao'),
(18, '1245555', 'lele', 2, 'eletrico');

-- --------------------------------------------------------

--
-- Estrutura para tabela `responsaveis`
--

CREATE TABLE `responsaveis` (
  `id` int(11) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `cargo` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `responsaveis`
--

INSERT INTO `responsaveis` (`id`, `nome`, `cargo`) VALUES
(1, 'Blaze', 'aluno');

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `movimentacao`
--
ALTER TABLE `movimentacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `FK_movimentacao_2` (`fk_produtos_id`),
  ADD KEY `FK_movimentacao_3` (`fk_responsaveis_id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `responsaveis`
--
ALTER TABLE `responsaveis`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `movimentacao`
--
ALTER TABLE `movimentacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de tabela `responsaveis`
--
ALTER TABLE `responsaveis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `movimentacao`
--
ALTER TABLE `movimentacao`
  ADD CONSTRAINT `FK_movimentacao_2` FOREIGN KEY (`fk_produtos_id`) REFERENCES `produtos` (`id`),
  ADD CONSTRAINT `FK_movimentacao_3` FOREIGN KEY (`fk_responsaveis_id`) REFERENCES `responsaveis` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
