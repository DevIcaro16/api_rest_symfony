# 🚗 Car API - Symfony 7 + PostgreSQL + Docker

Uma API RESTful desenvolvida com **Symfony 7**, utilizando **Doctrine ORM** e conectada ao banco de dados **PostgreSQL 16-alpine** via **Docker Compose**.  
A API realiza operações CRUD (Create, Read, Update, Delete) de **carros**.

---

## 🧰 Tecnologias Utilizadas

- [Symfony 7](https://symfony.com/)
- [Doctrine ORM](https://www.doctrine-project.org/)
- [PostgreSQL 16-alpine](https://hub.docker.com/_/postgres)
- [Docker + Docker Compose](https://docs.docker.com/compose/)
- [PHP 8.2+](https://www.php.net/)
- [Composer](https://getcomposer.org/)

---

## 🚀 Como Executar o Projeto

Siga os passos abaixo para rodar o projeto localmente:

### 1. Clonar o repositório

```bash
git clone https://github.com/DevIcaro16/api_rest_symfony.git
cd api_rest_symfony
```

### 2. Instalar as dependências do PHP

```bash
composer install
```

> É necessário ter o PHP 8.2+ instalado localmente ou usar um container com Composer.

### 3. Configurar o arquivo `.env`

Crie ou edite o arquivo `.env` ou `.env.local` com as seguintes variáveis:

```dotenv
APP_ENV=dev
APP_SECRET=YourSecretKey

POSTGRES_DB=automotive
POSTGRES_USER=postgres
POSTGRES_PASSWORD=your_password
POSTGRES_HOST=database
POSTGRES_PORT=5432
POSTGRES_VERSION=16

DATABASE_URL="postgresql://${POSTGRES_USER}:${POSTGRES_PASSWORD}@${POSTGRES_HOST}:${POSTGRES_PORT}/${POSTGRES_DB}?serverVersion=${POSTGRES_VERSION}&charset=utf8"
```

### 4. Subir os containers Docker

```bash
docker-compose up -d
```

> Isso criará os containers necessários, incluindo o PostgreSQL.

### 5. Executar as migrations do banco de dados

```bash
symfony console doctrine:migrations:migrate
```

### 6. Iniciar o servidor Symfony

```bash
symfony server:start
```

---

## 📡 Endpoints da API

| Método | Rota              | Descrição                     |
|--------|-------------------|-------------------------------|
| GET    | `/cars`           | Listar todos os carros        |
| GET    | `/cars/{id}`      | Buscar carro pelo ID          |
| POST   | `/car`            | Criar novo carro              |
| PUT    | `/cars/{id}`      | Atualizar todos os dados      |
| PATCH  | `/cars/{id}`      | Atualizar parcialmente        |
| DELETE | `/cars/{id}`      | Deletar um carro              |

---

## 📦 Exemplo de JSON para criação

```json
{
  "brand": "Volkswagen",
  "model": "Gol 1.0",
  "manufacture_year": 2024,
  "model_year": 2025,
  "km": 0.0
}
```

---

## 📂 Estrutura do Projeto

```
src/
  ├── Controller/        # Controladores da API
  ├── Entity/            # Entidades do Doctrine
  ├── Repository/        # Repositórios customizados
  └── ...
```

---


## 🧑‍💻 Autor

Desenvolvido por **Ícaro Rebouças**  
📧 [icaroip15@gmail.com](mailto:icaroip15@gmail.com)  
🔗 [linkedin.com/in/ícaro-rebouças-0b1998297](www.linkedin.com/in/ícaro-rebouças-0b1998297)

---