CREATE DATABASE IF NOT EXISTS gerenciador_logistico
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gerenciador_logistico;

CREATE TABLE IF NOT EXISTS produtos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    descricao VARCHAR(255),
    peso_kg DECIMAL(10, 2) NOT NULL
);
CREATE TABLE IF NOT EXISTS enderecos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    identificacao VARCHAR(120) NOT NULL,
    rua VARCHAR(150) NOT NULL,
    numero VARCHAR(20) NOT NULL,
    cep CHAR(9) NOT NULL,
    cidade VARCHAR(100) NOT NULL,
    uf CHAR(2) NOT NULL
);
CREATE TABLE IF NOT EXISTS gerenciador_logistico.transportadores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(120) NOT NULL,
    telefone VARCHAR(20) NOT NULL
);
CREATE TABLE IF NOT EXISTS gerenciador_logistico.fretes (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    data_frete DATE NOT NULL,
    origem_id INT UNSIGNED NOT NULL,
    destino_id INT UNSIGNED NOT NULL,
    transportador_id INT UNSIGNED NOT NULL,
    FOREIGN KEY (origem_id) REFERENCES gerenciador_logistico.enderecos(id),
    FOREIGN KEY (destino_id) REFERENCES gerenciador_logistico.enderecos(id),
    FOREIGN KEY (transportador_id) REFERENCES gerenciador_logistico.transportadores(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS gerenciador_logistico.frete_produtos (
    frete_id INT UNSIGNED NOT NULL,
    produto_id INT UNSIGNED NOT NULL,
    PRIMARY KEY (frete_id, produto_id),
    FOREIGN KEY (frete_id) REFERENCES gerenciador_logistico.fretes(id),
    FOREIGN KEY (produto_id) REFERENCES gerenciador_logistico.produtos(id)
) ENGINE=InnoDB;