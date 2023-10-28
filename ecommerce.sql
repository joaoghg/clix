-- MySQL Script generated by MySQL Workbench
-- Mon Oct  9 00:08:13 2023
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema ecommerce
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema ecommerce
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `ecommerce` DEFAULT CHARACTER SET utf8 ;
USE `ecommerce` ;

-- -----------------------------------------------------
-- Table `ecommerce`.`pessoas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`pessoas` (
  `ps_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `ps_nome` VARCHAR(64) NOT NULL,
  `ps_email` VARCHAR(128) NULL DEFAULT NULL,
  `ps_contato` BIGINT(20) NULL DEFAULT NULL,
  `ps_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ps_codigo`),
  UNIQUE INDEX `desemail_UNIQUE` (`ps_email` ASC))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`enderecos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`enderecos` (
  `end_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `ps_codigo` INT(11) NOT NULL,
  `end_rua` VARCHAR(128) NOT NULL,
  `end_complemento` VARCHAR(32) NULL DEFAULT NULL,
  `end_cidade` VARCHAR(32) NOT NULL,
  `end_estado` VARCHAR(32) NOT NULL,
  `end_pais` VARCHAR(32) NOT NULL,
  `end_cep` INT(11) NOT NULL,
  `end_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`end_codigo`),
  INDEX `fk_addresses_persons_idx` (`ps_codigo` ASC),
  CONSTRAINT `fk_addresses_persons`
    FOREIGN KEY (`ps_codigo`)
    REFERENCES `ecommerce`.`pessoas` (`ps_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`usuarios` (
  `user_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `ps_codigo` INT(11) NOT NULL,
  `user_login` VARCHAR(64) NOT NULL,
  `user_senha` VARCHAR(256) NOT NULL,
  `user_admin` TINYINT(4) NOT NULL DEFAULT '0',
  `user_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_codigo`),
  INDEX `FK_users_persons_idx` (`ps_codigo` ASC),
  CONSTRAINT `fk_users_persons`
    FOREIGN KEY (`ps_codigo`)
    REFERENCES `ecommerce`.`pessoas` (`ps_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`carrinhos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`carrinhos` (
  `car_codigo` INT(11) NOT NULL,
  `car_sessao_id` VARCHAR(64) NOT NULL,
  `user_codigo` INT(11) NULL DEFAULT NULL,
  `end_codigo` INT(11) NULL DEFAULT NULL,
  `car_frete` DECIMAL(10,2) NULL DEFAULT NULL,
  `car_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`car_codigo`),
  INDEX `FK_carts_users_idx` (`user_codigo` ASC),
  INDEX `fk_carts_addresses_idx` (`end_codigo` ASC),
  CONSTRAINT `fk_carts_addresses`
    FOREIGN KEY (`end_codigo`)
    REFERENCES `ecommerce`.`enderecos` (`end_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_carts_users`
    FOREIGN KEY (`user_codigo`)
    REFERENCES `ecommerce`.`usuarios` (`user_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`produtos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`produtos` (
  `prd_codigo` INT(11) NOT NULL,
  `prd_descricao` VARCHAR(64) NOT NULL,
  `prd_preco` DECIMAL(10,2) NOT NULL,
  `prd_largura` DECIMAL(10,2) NOT NULL,
  `prd_altura` DECIMAL(10,2) NOT NULL,
  `prd_comprimento` DECIMAL(10,2) NOT NULL,
  `prd_peso` DECIMAL(10,2) NOT NULL,
  `prd_url` VARCHAR(128) NOT NULL,
  `prd_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`prd_codigo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`produtos_carrinho`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`produtos_carrinho` (
  `carprd_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `car_codigo` INT(11) NOT NULL,
  `prd_codigo` INT(11) NOT NULL,
  `carprd_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`carprd_codigo`),
  INDEX `FK_cartsproducts_carts_idx` (`car_codigo` ASC),
  INDEX `FK_cartsproducts_products_idx` (`prd_codigo` ASC),
  CONSTRAINT `fk_cartsproducts_carts`
    FOREIGN KEY (`car_codigo`)
    REFERENCES `ecommerce`.`carrinhos` (`car_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_cartsproducts_products`
    FOREIGN KEY (`prd_codigo`)
    REFERENCES `ecommerce`.`produtos` (`prd_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`categorias`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`categorias` (
  `cat_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `cat_descricao` VARCHAR(32) NOT NULL,
  `cat_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`cat_codigo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`pedido_status`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`pedido_status` (
  `pedst_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `pedst_descricao` VARCHAR(32) NOT NULL,
  `pedst_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`pedst_codigo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`pedidos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`pedidos` (
  `ped_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `car_codigo` INT(11) NOT NULL,
  `user_codigo` INT(11) NOT NULL,
  `ped_status` INT(11) NOT NULL,
  `ped_vl_total` DECIMAL(10,2) NOT NULL,
  `ped_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ped_codigo`),
  INDEX `FK_orders_carts_idx` (`car_codigo` ASC),
  INDEX `FK_orders_users_idx` (`user_codigo` ASC),
  INDEX `fk_orders_ordersstatus_idx` (`ped_status` ASC),
  CONSTRAINT `fk_orders_carts`
    FOREIGN KEY (`car_codigo`)
    REFERENCES `ecommerce`.`carrinhos` (`car_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_ordersstatus`
    FOREIGN KEY (`ped_status`)
    REFERENCES `ecommerce`.`pedido_status` (`pedst_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_orders_users`
    FOREIGN KEY (`user_codigo`)
    REFERENCES `ecommerce`.`usuarios` (`user_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`produto_categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`produto_categoria` (
  `cat_codigo` INT(11) NOT NULL,
  `prd_codigo` INT(11) NOT NULL,
  PRIMARY KEY (`cat_codigo`, `prd_codigo`),
  INDEX `fk_productscategories_products_idx` (`prd_codigo` ASC),
  CONSTRAINT `fk_productscategories_categories`
    FOREIGN KEY (`cat_codigo`)
    REFERENCES `ecommerce`.`categorias` (`cat_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_productscategories_products`
    FOREIGN KEY (`prd_codigo`)
    REFERENCES `ecommerce`.`produtos` (`prd_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`produto_imagens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`produto_imagens` (
   `img_codigo` INT(11) NOT NULL,
   `prd_codigo` INT(11) NOT NULL,
   `img_caminho` VARCHAR(255) NOT NULL,
   PRIMARY KEY (img_codigo),
   CONSTRAINT `fk_productsimages_produtos`
     FOREIGN KEY (`prd_codigo`)
     REFERENCES `ecommerce`.`produtos` (`prd_codigo`)
     ON DELETE NO ACTION
     ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`log_usuarios`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`log_usuarios` (
  `userlog_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `user_codigo` INT(11) NOT NULL,
  `userlog_descricao` VARCHAR(128) NOT NULL,
  `userlog_ip` VARCHAR(45) NOT NULL,
  `userlog_responsavel` VARCHAR(128) NOT NULL,
  `userlog_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`userlog_codigo`),
  INDEX `fk_userslogs_users_idx` (`user_codigo` ASC),
  CONSTRAINT `fk_userslogs_users`
    FOREIGN KEY (`user_codigo`)
    REFERENCES `ecommerce`.`usuarios` (`user_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;


-- -----------------------------------------------------
-- Table `ecommerce`.`log_recuperar_senha`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `ecommerce`.`log_recuperar_senha` (
  `logrs_codigo` INT(11) NOT NULL AUTO_INCREMENT,
  `user_codigo` INT(11) NOT NULL,
  `logrs_ip` VARCHAR(45) NOT NULL,
  `logrs_data` DATETIME NULL DEFAULT NULL,
  `logrs_registro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`logrs_codigo`),
  INDEX `fk_userspasswordsrecoveries_users_idx` (`user_codigo` ASC),
  CONSTRAINT `fk_userspasswordsrecoveries_users`
    FOREIGN KEY (`user_codigo`)
    REFERENCES `ecommerce`.`usuarios` (`user_codigo`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8;

insert into pessoas (ps_nome, ps_email, ps_contato) values ('admin', 'admin@gmail.com', '1440028922');
insert into usuarios(ps_codigo, user_login, user_senha, user_admin) values (1, 'admin', '$2y$12$N8hBTDJAc33fostLXYYrC.r1bMjjGWRS7urjAQA.V0ozP71wTyRK6', 1);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;