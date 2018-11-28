-- phpMyAdmin SQL Dump
-- version 4.8.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 28-Nov-2018 às 21:04
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
  `email` varchar(100) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `cliente`
--

INSERT INTO `cliente` (`idcliente`, `nome`, `cpf`, `email`) VALUES
(2, 'Alfredo Afonso', '00000000000', 'asdasds@hotmail.com'),
(3, 'dfsdfsdf', '00000000000', 'asdasdasdads'),
(4, 'NANO', '54546546545', 'guasd@hotmail.com'),
(5, 'Valdir Gosmatti de Lima', '00000000000', 'valdir@hotmail.com');

-- --------------------------------------------------------

--
-- Estrutura da tabela `funcionario`
--

CREATE TABLE `funcionario` (
  `idfuncionario` int(11) NOT NULL,
  `nome` varchar(150) NOT NULL,
  `cpf` varchar(11) NOT NULL DEFAULT '',
  `cargo` varchar(150) NOT NULL,
  `status` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `funcionario`
--

INSERT INTO `funcionario` (`idfuncionario`, `nome`, `cpf`, `cargo`, `status`) VALUES
(6, 'Gusatvo', '00000000000', 'asdsad', 0);

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
(87, 3, 10.00, 6, '181128180228FA');

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
  `idprodutoestoque` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `lanche`
--

INSERT INTO `lanche` (`idlanche`, `nome`, `descricao`, `preco`, `lancheqtd`, `idprodutoestoque`) VALUES
(2, 'coxinha', 'tem frango', 3.00, 5, NULL),
(3, 'x tudo', 'tem tudo', 10.00, 2, NULL),
(14, 'X Salada', 'Alface, tomate, queijo ...', 5.00, 0, NULL),
(17, 'dasdasd', 'nao existe', 15.00, 10, NULL),
(18, 'x burguer', 'tem carne', 4.50, 0, NULL),
(19, 'Quibe', 'De carne', 6.50, 8, NULL);

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
(2, '181128174628SV', '2018-11-27', 4, 6),
(3, '181128180228FA', '2018-11-29', 5, 6);

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
  MODIFY `idcliente` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `funcionario`
--
ALTER TABLE `funcionario`
  MODIFY `idfuncionario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `itens`
--
ALTER TABLE `itens`
  MODIFY `iditem` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `lanche`
--
ALTER TABLE `lanche`
  MODIFY `idlanche` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `pedidos`
--
ALTER TABLE `pedidos`
  MODIFY `idpedido` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
