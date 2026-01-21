# Gerenciamento de Produtos

Aplicação web para o gerenciamento de "Produtos" utilizando Laravel e Docker. Desafio técnico para a First Decision.

## Descrição

Este projeto é uma infraestrutura containerizada que fornece um ambiente completo para gerenciamento de produtos. A solução integra múltiplos componentes que trabalham juntos para oferecer uma experiência de desenvolvimento e implantação eficiente.

### Componentes da Arquitetura

1. **app**
   
   Contêiner responsável por hospedar a aplicação Laravel em PHP 8.1. Inclui Xdebug para facilitar o desenvolvimento e depuração, além de suporte completo para execução de testes automatizados.

2. **db**
   
   Contêiner MariaDB dedicado ao armazenamento persistente dos dados da aplicação. Garante a integridade e segurança das informações relacionadas a produtos, usuários e tokens de autenticação.

3. **nginx**
   
   Servidor web que atua como proxy reverso, direcionando requisições HTTP/HTTPS para a aplicação PHP. Oferece otimização de desempenho e uma camada adicional de segurança.

4. **phpmyadmin**
   
   Interface web para administração do banco de dados MariaDB. Facilita tarefas de gerenciamento, visualização e manipulação de dados de forma intuitiva.

## Licença

Este projeto é licenciado sob a [Licença MIT](LICENSE). Consulte o arquivo [LICENSE](LICENSE) para obter mais detalhes.

### Uso Permitido

Você está autorizado a utilizar este código-fonte apenas para fins de estudo e aprendizado. Isso inclui a análise, modificação e execução do software, desde que seja para fins educacionais.

### Restrições de Uso

Você não tem permissão para usar, reproduzir ou compartilhar este projeto para fins comerciais sem autorização prévia.

### Responsabilidade

O autor deste projeto não assume nenhuma responsabilidade pelo uso indevido ou violação dos termos de licença. Você é o único responsável por garantir o uso adequado e ético deste código-fonte.

### Isenção de Garantia

Este projeto é fornecido "no estado em que se encontra", sem garantias de qualquer tipo. O autor não se responsabiliza por quaisquer danos ou consequências decorrentes do uso deste software.

## Instruções

Siga as etapas abaixo para configurar e executar o projeto:

1. Clone o repositório:
   ```bash
   git clone https://github.com/kleberalv/project-products.git
   cd project-products
   ```

2. Copie o arquivo .env para configurar as variáveis de ambiente:
   ```bash
   cp .env.example .env
   ```

3. Construa e inicie os contêineres Docker:
   ```bash
   docker-compose up -d --build
   ```

4. Aguarde a conclusão do setup automático. O Docker executará automaticamente:
   - Instalação de dependências PHP (composer)
   - Geração da chave da aplicação
   - Execução de migrations
   - Seed do banco de dados

5. A aplicação estará pronta para uso em:
   - **Interface Web**: http://localhost:8080
   - **API**: http://localhost:8080/api
   - **phpMyAdmin**: http://localhost:8090

### Executar Testes

Para executar os testes automatizados:

```bash
docker exec -it application-server-app php artisan test
```

**Resultado esperado:** 50 testes passando com mais de 160 assertions em aproximadamente 13 segundos.

Para rodar um teste específico:

```bash
docker exec -it application-server-app php artisan test --filter NomeDoTeste
```

**Exemplo:**
```bash
docker exec -it application-server-app php artisan test --filter pode_criar_produto
```

---

## 📡 Documentação da API

A API REST está **completamente documentada de forma interativa** com Swagger/OpenAPI 3.0. Toda a documentação, exemplos e testes estão disponíveis em:

### 🔗 Acessar Documentação Interativa

```
http://localhost:8080/api/documentation
```

**Você pode:** testar endpoints, visualizar respostas, enviar requisições autenticadas tudo na interface do navegador!

---

## 📚 Documentação Interativa com Swagger

A API conta com documentação interativa completa gerada com **L5-Swagger** (OpenAPI 3.0). Você pode explorar e testar todos os endpoints diretamente pelo navegador.

### Acessar o Swagger UI

```
http://localhost:8080/api/documentation
```

### Funcionalidades do Swagger

✅ **Visualizar todos os endpoints** - Organizados por tags (Autenticação, Produtos)  
✅ **Ver detalhes das requisições e respostas** - Parâmetros, headers, corpos de requisição  
✅ **Testar endpoints interativamente** - Botão "Try it out" para executar requisições  
✅ **Autenticação integrada** - Adicione seu token Bearer direto na interface  
✅ **Modelos de esquema** - Definições de estruturas JSON (Product, User, etc)  

### Exemplo de Uso no Swagger

1. Abra http://localhost:8080/api/documentation
2. Localize o endpoint **POST /api/login**
3. Clique em "Try it out"
4. Preencha com credenciais de teste:
   ```json
   {
     "email": "admin@test.com",
     "password": "password"
   }
   ```
5. Clique "Execute" e copie o token retornado
6. Clique no cadeado ⚙️ no topo e cole o token (formato: `Bearer SEU_TOKEN`)
7. Agora teste os endpoints protegidos!

---

## Tecnologias Utilizadas

<div align="left">
    <img align="center" alt="PHP" src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white">
    <img align="center" alt="Laravel" src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white">
    <img align="center" alt="MySQL" src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white">
    <img align="center" alt="Bootstrap" src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white">
    <img align="center" alt="API REST" src="https://img.shields.io/badge/API_REST-009688?style=for-the-badge">
</div>

## Ferramentas de Desenvolvimento Utilizadas

<div align="left">
    <img align="center" alt="Docker" src="https://img.shields.io/badge/docker-%230db7ed.svg?style=for-the-badge&logo=docker&logoColor=white">
    <img align="center" alt="Composer" src="https://img.shields.io/badge/Composer-885630?style=for-the-badge&logo=composer&logoColor=white">
    <img align="center" alt="MariaDB" src="https://img.shields.io/badge/MariaDB-003545?style=for-the-badge&logo=mariadb&logoColor=white">
    <img align="center" alt="phpMyAdmin" src="https://img.shields.io/badge/phpMyAdmin-4479A1?style=for-the-badge&logo=phpmyadmin&logoColor=white">
    <img align="center" alt="PHPUnit" src="https://img.shields.io/badge/PHPUnit-366488?style=for-the-badge&logo=php&logoColor=white">
    <img align="center" alt="Swagger" src="https://img.shields.io/badge/Swagger-85EA2D?style=for-the-badge&logo=swagger&logoColor=white">
</div>

---

# Copyright ©

Copyright © Developed by: Kleber Alves Bezerera Junior - Sênior Developer 2026.