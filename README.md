# 📦 Gerenciador de Itens - LaraDocker

Sistema completo de gerenciamento de itens com API RESTful desenvolvido em Laravel e interface moderna em Vue.js, totalmente containerizado com Docker.

## 📋 Sobre o Projeto

Este é um sistema de gerenciamento de itens que permite criar, visualizar, editar e excluir produtos em estoque. O projeto foi desenvolvido seguindo as melhores práticas de desenvolvimento, com arquitetura em camadas (Service Layer Pattern), validações robustas e interface responsiva.

### ✨ Funcionalidades

- CRUD completo de itens (Criar, Listar, Visualizar, Editar, Deletar)
- Dashboard com resumo de estoque (total de itens, quantidade e valor total)
- Modal de visualização detalhada de itens
- Paginação automática (10 itens por página)
- Sistema de categorias predefinidas
- Validação de dados no backend com FormRequest
- Interface moderna e responsiva com Bootstrap 5
- Testes automatizados
- Totalmente containerizado com Docker

## 🛠️ Tecnologias Utilizadas

### Backend
- **PHP 8.3** - Linguagem de programação
- **Laravel 13** - Framework PHP
- **Laravel Sanctum** - Autenticação de API
- **MySQL** - Banco de dados (via migrations)
- **PHPUnit** - Testes automatizados

### Frontend
- **Vue.js 3** - Framework JavaScript reativo
- **Vite** - Build tool e dev server
- **Axios** - Cliente HTTP para consumir a API
- **Bootstrap 5** - Framework CSS para estilização
- **SweetAlert2** - Alertas e modais elegantes

### DevOps & Infraestrutura
- **Docker** - Containerização
- **Docker Compose** - Orquestração de containers
- **Nginx** - Servidor web
- **PHP-FPM** - FastCGI Process Manager

### Ferramentas de Desenvolvimento
- **Laravel Pint** - Code formatter
- **Faker** - Geração de dados fake para testes
- **Make** - Automação de comandos


### Padrões e Práticas Utilizadas

- **Service Layer Pattern** - Lógica de negócio separada dos controllers
- **Repository Pattern** (via Eloquent ORM)
- **Form Request Validation** - Validação centralizada e reutilizável
- **API Response Trait** - Respostas padronizadas da API
- **Factory Pattern** - Geração de dados para testes e seeds
- **RESTful API** - Endpoints semânticos seguindo padrões REST

## 📦 Pré-requisitos

Antes de começar, certifique-se de ter instalado:

- [Docker](https://www.docker.com/get-started) (versão 20.x ou superior)
- [Docker Compose](https://docs.docker.com/compose/install/) (versão 2.x ou superior)
- [Make](https://www.gnu.org/software/make/) (geralmente já vem instalado no Linux/Mac)

**Não é necessário ter PHP, Composer, Node.js ou MySQL instalados localmente!** Tudo roda dentro dos containers Docker.

## 🚀 Como Rodar o Projeto

### 1. Clone o Repositório

```bash
git clone <seu-repositorio>
cd laradocker
```

### 2. Execute o Setup Completo

O projeto possui um Makefile que automatiza todo o processo de instalação:

```bash
make setup
```

Este comando irá:
1. ⬆️ Subir os containers Docker (app + nginx)
2. 📥 Instalar as dependências do Composer
3. 🔑 Gerar a chave da aplicação Laravel
4. 🗄️ Executar as migrations e seeders (criar banco e popular com dados)
5. 🎨 Instalar dependências do frontend e iniciar o servidor Vite

### 3. Acesse a Aplicação

Após o setup, você terá dois serviços rodando:

- **Frontend (Vue.js)**: http://localhost:5173
- **API (Laravel)**: http://localhost:8080/api

Abra o navegador e acesse: **http://localhost:5173**

## 📝 Comandos Disponíveis

O projeto possui os seguintes comandos Make para facilitar o desenvolvimento:

```bash
# Configurar e iniciar todo o projeto (primeira vez)
make setup

# Apenas iniciar o frontend (se já estiver configurado)
make frontend

# Executar os seeders (popular banco com dados)
make seed

# Executar os testes automatizados
make test

# Parar todos os containers
make down
```

### Comandos Docker Diretos

Se precisar executar comandos diretamente no container:

```bash
# Acessar o container da aplicação
docker exec -it laradocker_app bash

# Executar comandos Artisan
docker exec -it laradocker_app php artisan migrate
docker exec -it laradocker_app php artisan db:seed

# Executar Tinker
docker exec -it laradocker_app php artisan tinker

# Ver logs
docker logs laradocker_app
docker logs laradocker_nginx
```

## 🔌 API Endpoints

### Items

| Método | Endpoint | Descrição | Body |
|--------|----------|-----------|------|
| GET | `/api/items` | Lista todos os itens (paginado) | - |
| GET | `/api/items?page=2` | Lista itens da página 2 | - |
| GET | `/api/items/{id}` | Exibe um item específico | - |
| POST | `/api/items` | Cria um novo item | JSON com dados do item |
| PUT | `/api/items/{id}` | Atualiza um item | JSON com dados do item |
| DELETE | `/api/items/{id}` | Deleta um item | - |

### Estrutura do Item

```json
{
  "name": "Nome do item",           // obrigatório, min:3, max:255
  "code": "ABC123",                 // opcional, único, max:50
  "category": "Eletrônicos",        // opcional, deve ser uma das categorias válidas
  "description": "Descrição...",    // opcional
  "price": 99.90,                   // obrigatório, numérico, min:0
  "quantity": 10                    // obrigatório, inteiro, min:0
}
```

### Categorias Válidas

- Eletrônicos
- Alimentos
- Vestuário
- Móveis
- Livros
- Brinquedos
- Ferramentas
- Outros

### Exemplo de Resposta (GET /api/items)

```json
{
  "message": "Itens encontrados com sucesso!",
  "data": {
    "items": {
      "current_page": 1,
      "data": [
        {
          "id": 1,
          "name": "Notebook Dell",
          "code": "NTB001",
          "category": "Eletrônicos",
          "description": "Notebook para trabalho",
          "quantity": 5,
          "price": 3500.00,
          "created_at": "2026-05-05T18:00:00.000000Z",
          "updated_at": "2026-05-05T18:00:00.000000Z"
        }
      ],
      "total": 33,
      "per_page": 10,
      "current_page": 1,
      "last_page": 4
    },
    "summary": {
      "total_quantity": 561,
      "total_value": 16230.01
    }
  }
}
```

## 🧪 Testes

O projeto possui testes automatizados para garantir a qualidade do código:

```bash
# Executar todos os testes
make test

# Ou diretamente com PHPUnit
docker exec -it laradocker_app php artisan test

# Executar testes com cobertura
docker exec -it laradocker_app php artisan test --coverage
```

Os testes cobrem:
- ✅ CRUD completo de itens
- ✅ Validações de campos obrigatórios
- ✅ Validações de formato de dados
- ✅ Unicidade de código
- ✅ Testes de paginação


### Backend (ItemRequest.php)

- **name**: obrigatório, string, mínimo 3 caracteres, máximo 255
- **code**: opcional, string, máximo 50 caracteres, único
- **category**: opcional, string, deve ser uma das categorias válidas
- **description**: opcional, string
- **price**: obrigatório, numérico, mínimo 0
- **quantity**: obrigatório, inteiro, mínimo 0

Todas as validações retornam mensagens personalizadas em português.

### Frontend (ItemManager.vue)

- Validação de campos obrigatórios
- Exibição de erros de validação do backend
- Conversão correta de tipos (parseFloat, parseInt)

## 🎨 Interface

A interface foi desenvolvida com foco em usabilidade:

- **Dashboard**: Cards com resumo do estoque
- **Tabela**: Listagem paginada com 10 itens por página
- **Modal de Criação/Edição**: Formulário com validação em tempo real
- **Modal de Visualização**: Exibição completa dos dados do item
- **Select de Categorias**: Dropdown com categorias predefinidas
- **Paginação**: Navegação entre páginas
- **Confirmação de Exclusão**: Dialog antes de deletar

## 🐛 Troubleshooting

### Porta 8080 já está em uso

```bash
# Parar containers e alterar porta no docker-compose.yaml
make down
# Editar docker-compose.yaml e trocar "8080:80" para "8081:80"
make setup
```

### Frontend não carrega

```bash
# Verificar se o Vite está rodando
ps aux | grep vite

# Reiniciar o frontend
make frontend
```

### Erro de permissão no Laravel

```bash
# Ajustar permissões
docker exec -it laradocker_app chmod -R 775 storage bootstrap/cache
docker exec -it laradocker_app chown -R www-data:www-data storage bootstrap/cache
```

### Banco de dados vazio

```bash
# Executar migrations e seeders novamente
docker exec -it laradocker_app php artisan migrate:fresh --seed
```

## 📚 Documentação Adicional

- [Laravel 13 Documentation](https://laravel.com/docs/13.x)
- [Vue.js 3 Documentation](https://vuejs.org/)
- [Docker Documentation](https://docs.docker.com/)

## 👨‍💻 Desenvolvimento

### Adicionar uma nova migração

```bash
docker exec -it laradocker_app php artisan make:migration create_nova_tabela
docker exec -it laradocker_app php artisan migrate
```

### Criar novo controller

```bash
docker exec -it laradocker_app php artisan make:controller NomeController --resource
```

### Criar novo model com tudo

```bash
docker exec -it laradocker_app php artisan make:model Nome -mfsc
# -m = migration
# -f = factory
# -s = seeder
# -c = controller
```

## 📄 Licença

Este projeto está sob a licença MIT.

---

⭐ Desenvolvido com Laravel + Vue.js + Docker
