-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 06-Dez-2018 às 20:16
-- Versão do servidor: 10.1.36-MariaDB
-- versão do PHP: 7.2.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `bd_lanche`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `cliente`
--

CREATE TABLE `cliente` (
  `idcliente` int(11) NOT NULL,
  `nome` varchar(100) NOT NULL,
  `cpf` varchar(11) NOT NULL DEFAULT '',
  `email` varchar(100) DEFAULT '',
  `frequencia` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nome`, `cpf`, `email`, `frequencia`) VALUES
(11, 'Gustavo', '11640738932', 'gustavo.gosmatti@hotmail.com', 3),
(12, 'Valdir', '71365354920', 'valdr@hotmail.com', 1);

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionario`
--

CREATE TABLE `funcionario` (
  `idfuncionario` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(11) NOT NULL DEFAULT '',
  `cargo` varchar(150) NOT NULL,
  `status` bit(1) NOT NULL DEFAULT b'0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `funcionario`
--

INSERT INTO `funcionario` (`idfuncionario`, `nome`, `cpf`, `cargo`, `status`) VALUES
(1, 'Alfredo Azevedo', '12345678910', 'Balconista', b'1'),
(2, 'Carlos', '98745632114', 'Balconista', b'1'),
(3, 'Ana', '54789455212', 'Balconista', b'1'),
(4, 'Bruno', '54146574563', 'Balconista', b'0'),
(6, 'Tiago', '86756437054', 'Balconista', b'1');

-- --------------------------------------------------------

--
-- Estrutura da tabela `itens`
--

CREATE TABLE `itens` (
  `iditem` int(11) NOT NULL,
  `itemproduto` int(11) NOT NULL DEFAULT '0',
  `itemvalor` double(9,2) NOT NULL DEFAULT '0.00',
  `itemqtd` int(3) NOT NULL DEFAULT '0',
  `itemcodpedido` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `itens`
--

INSERT INTO `itens` (`iditem`, `itemproduto`, `itemvalor`, `itemqtd`, `itemcodpedido`) VALUES
(63, 3, 5.50, 2, '181125172225WV'),
(64, 17, 15.00, 7, '181125172225WV'),
(76, 19, 6.50, 2, '181128110928OJ'),
(77, 14, 5.00, 3, '181128110928OJ'),
(80, 2, 3.00, 3, '181128110928OJ'),
(81, 2, 3.00, 1, '181128150528UO'),
(82, 14, 5.00, 2, '181128150528UO'),
(83, 14, 5.00, 5, '181128150628FJ'),
(84, 3, 10.00, 2, '181128150628FJ'),
(85, 18, 4.50, 10, '181128151128YT'),
(86, 2, 3.00, 1, '181128174628SV'),
(87, 3, 10.00, 6, '181128180228FA'),
(88, 19, 6.50, 8, '181128181928XU'),
(89, 19, 6.50, 100, '181128190528AU'),
(90, 21, 50.00, 50, '181128191628FY'),
(92, 4, 3.50, 3, '181206091906HC'),
(93, 1, 3.50, 1, '181206091906HC'),
(94, 5, 6.00, 1, '181206091906HC'),
(95, 5, 6.00, 2, '181206092306DE'),
(96, 4, 3.50, 5, '181206092306DE'),
(97, 2, 4.00, 4, '181206092406HB'),
(98, 5, 6.00, 2, '181206092406HB'),
(99, 2, 4.00, 5, '181206092706JL'),
(100, 4, 3.50, 4, '181206092706JL'),
(101, 5, 6.00, 4, '181206092706JL'),
(102, 5, 6.00, 3, '181206092806SL'),
(103, 4, 3.50, 5, '181206092806SL'),
(104, 5, 6.00, 1, '181206093006HH'),
(105, 1, 3.50, 5, '181206093006HH'),
(106, 1, 3.50, 7, '181206114306FI'),
(107, 5, 6.00, 5, '181206114306FI'),
(109, 4, 3.50, 3, '181206125606UW'),
(110, 5, 6.00, 1, '181206125606UW'),
(113, 1, 3.50, 10, '181206125606UW'),
(114, 2, 4.00, 7, '181206125606UW'),
(115, 6, 4.50, 7, '181206092806SL'),
(116, 1, 3.50, 1, '181206092806SL'),
(117, 6, 4.50, 3, '181206091906HC'),
(118, 2, 4.00, 10, '181206132906UC'),
(119, 1, 3.50, 10, '181206142406DV'),
(120, 5, 6.00, 3, '181206142406DV'),
(121, 5, 6.00, 4, '181206164906CV');

-- --------------------------------------------------------

--
-- Estrutura da tabela `lanche`
--

CREATE TABLE `lanche` (
  `idlanche` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `descricao` varchar(150) NOT NULL,
  `preco` double(9,2) DEFAULT '0.00',
  `lancheqtd` int(11) NOT NULL DEFAULT '0',
  `idprodutoestoque` int(11) DEFAULT NULL,
  `vezespedido` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `lanche`
--

INSERT INTO `lanche` (`idlanche`, `nome`, `descricao`, `preco`, `lancheqtd`, `idprodutoestoque`, `vezespedido`) VALUES
(1, 'COXINHA', 'Frango com catupiry', 3.50, 30, NULL, 0),
(2, 'PASTEL', 'Carne', 4.00, 30, NULL, 0),
(4, 'QUIBE', '', 3.50, 30, NULL, 0),
(5, 'COCA COLA 2L', '', 6.00, 30, NULL, 0),
(6, 'ESFIRRA', 'Frango', 4.50, 30, NULL, 0),
(7, 'COCA COLA 1L', '', 4.00, 20, NULL, 0);

-- --------------------------------------------------------

--
-- Estrutura da tabela `pedidos`
--

CREATE TABLE `pedidos` (
  `idpedido` int(11) NOT NULL,
  `codigopedido` varchar(20) DEFAULT NULL,
  `datapedido` date DEFAULT NULL,
  `idcliente` int(11) DEFAULT '0',
  `idfuncionario` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `pedidos`
--

INSERT INTO `pedidos` (`idpedido`, `codigopedido`, `datapedido`, `idcliente`, `idfuncionario`) VALUES
(12, '181206170406LZ', '2018-12-05', 12, 2),
(13, '181206171406PF', '2018-12-06', 11, 2),
(14, '181206171506VF', '2018-12-05', 11, 2),
(15, '181206171506MN', '2018-12-05', 11, 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `phpgen_user_perms`
--

CREATE TABLE `phpgen_user_perms` (
  `user_id` int(11) NOT NULL,
  `page_name` varchar(255) COLLATE utf8_bin NOT NULL,
  `perm_name` varchar(6) COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Extraindo dados da tabela `phpgen_user_perms`
--

INSERT INTO `phpgen_user_perms` (`user_id`, `page_name`, `perm_name`) VALUES
(1, '', 'ADMIN');

-- --------------------------------------------------------

--
-- Estrutura da tabela `produtoestoque`
--

CREATE TABLE `produtoestoque` (
  `idprodutoestoque` int(11) NOT NULL,
  `tipounidade` varchar(100) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `preco` float NOT NULL,
  `estoqueminimo` float NOT NULL,
  `quantidadeestoque` float NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `produtoestoque`
--

INSERT INTO `produtoestoque` (`idprodutoestoque`, `tipounidade`, `nome`, `preco`, `estoqueminimo`, `quantidadeestoque`) VALUES
(1, 'KG', 'bacon', 5687.65, 5, 10);

-- --------------------------------------------------------

--
-- Estrutura da tabela `usuarios`
--

CREATE TABLE `usuarios` (
  `idusuario` int(11) NOT NULL,
  `usuarionome` varchar(255) NOT NULL DEFAULT '',
  `usuarioemail` varchar(255) NOT NULL DEFAULT '',
  `usuariosenha` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `usuarios`
--

INSERT INTO `usuarios` (`idusuario`, `usuarionome`, `usuarioemail`, `usuariosenha`) VALUES
(1, 'gglanches', 'gglanches@hotmail.com', '43c020d010aad70ed24f4c51a3bfccee');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`idcliente`);

--
-- Indexes for table `funcionario`
--
ALTER TABLE `funcionario`
  ADD PRIMARY KEY (`idfuncionario`);

--
-- Indexes for table `itens`
--
ALTER TABLE `itens`
  ADD PRIMARY KEY (`iditem`);

--
-- Indexes for table `lanche`
--
ALTER TABLE `lanche`
  ADD PRIMARY KEY (`idlanche`),
  ADD KEY `fk_lanche_produtoestoque` (`idprodutoestoque`);

--
-- Indexes for table `pedidos`
--
ALTER TABLE `pedidos`
  ADD PRIMARY KEY (`idpedido`);

--
-- Indexes for table `phpgen_user_perms`
--
ALTER TABLE `phpgen_user_perms`
  ADD PRIMARY KEY (`user_id`,`page_name`,`perm_name`);

--
-- Indexes for table `produtoestoque`
--
ALTER TABLE `produtoestoque`
  ADD PRIMARY KEY (`idprodutoestoque`);

--
-- Indexes for table `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`idusuario`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cliente`
--
ALTER TABLE `cliente`
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `idfuncionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `itens`
--
ALTER TABLE `itens`
  MODIFY `iditem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=122;

--
-- AUTO_INCREMENT for table `lanche`
--
ALTER TABLE `lanche`
  MODIFY `idlanche` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `idpedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `produtoestoque`
--
ALTER TABLE `produtoestoque`
  MODIFY `idprodutoestoque` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `idusuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Limitadores para a tabela `lanche`
--
ALTER TABLE `lanche`
  ADD CONSTRAINT `fk_lanche_produtoestoque` FOREIGN KEY (`idprodutoestoque`) REFERENCES `produtoestoque` (`idprodutoestoque`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
