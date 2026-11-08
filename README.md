# Gerenciador Logístico

## Sobre o projeto

O Gerenciador Logístico é um sistema web que desenvolvi para organizar algumas atividades de transporte de mercadorias.

A ideia do projeto é permitir o cadastro de produtos, endereços e transportadores, além de registrar os fretes realizados e gerar relatórios.

Esse projeto faz parte da minha atividade acadêmica (APO) do curso de Engenharia de Software.

## Tecnologias utilizadas

Para desenvolver o sistema, utilizei:

- **PHP:** para o funcionamento do sistema e a comunicação com o banco de dados.
- **MySQL:** para armazenar as informações cadastradas.
- **HTML e CSS:** para montar e organizar as páginas.
- **Bootstrap:** para melhorar a aparência do sistema.
- **JavaScript:** para adicionar e remover produtos no cadastro de fretes.
- **XAMPP:** para executar o projeto no meu computador.
- **Git e GitHub:** para organizar e guardar as versões do código.

## O que o sistema faz

O sistema possui algumas funcionalidades principais:

**Cadastro de produtos:** permite cadastrar produtos com nome, descrição e peso, além de consultar e editar os registros.

**Cadastro de endereços:** permite registrar os endereços que serão utilizados como origem e destino dos fretes.

**Cadastro de transportadores:** permite cadastrar e editar os dados das transportadoras.

**Cadastro de fretes:** permite selecionar a data, a origem, o destino, o transportador e os produtos que fazem parte do frete.

**Relatório diário:** permite escolher uma data e baixar um arquivo CSV com os fretes cadastrados naquele dia.

## Banco de dados

Utilizei o MySQL para armazenar os dados do sistema.

O banco foi dividido em cinco tabelas:

- produtos
- enderecos
- transportadores
- fretes
- frete_produtos

Essas tabelas possuem relacionamentos para que as informações dos fretes fiquem ligadas aos produtos, endereços e transportadores cadastrados.

O arquivo `schema.sql`, que está na pasta `database`, contém a estrutura do banco de dados.

## Como executar

Para executar o projeto no computador:

1. Instalar o XAMPP.
2. Iniciar o Apache e o MySQL.
3. Colocar a pasta do projeto dentro de `htdocs`.
4. Abrir o phpMyAdmin e importar o arquivo `database/schema.sql`.
5. Verificar as configurações do arquivo `database/conexao.php`.
6. Abrir o navegador e acessar:

`http://localhost/gerenciador-logistico/`

## O que aprendi com o projeto

Durante o desenvolvimento, consegui entender melhor como funciona a ligação entre as páginas de um sistema e o banco de dados.

Também pratiquei a criação de formulários, consultas SQL, validação de informações e organização dos arquivos PHP.

Uma das partes que mais exigiu atenção foi o cadastro de fretes, porque precisei relacionar os produtos, os endereços e os transportadores sem misturar as informações.

Outro aprendizado importante foi testar cada funcionalidade, identificar erros e fazer as correções necessárias.

## Autor

Wagner José Pedroso

Acadêmico de Engenharia de Software — UNIPAR