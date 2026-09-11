# SQL Injection – Laboratório Prático

Projeto desenvolvido para fins educacionais, demonstrando como uma aplicação PHP pode ficar vulnerável a SQL Injection quando dados fornecidos pelo usuário são inseridos diretamente em uma consulta SQL.

> ⚠️ **Atenção:** este projeto é intencionalmente vulnerável. Utilize somente em ambiente local de laboratório, como XAMPP. Não publique esta aplicação em um servidor real.

## 📚 Objetivo

O objetivo deste laboratório é compreender:

- O que é SQL Injection;
- Como os dados de um formulário chegam ao PHP;
- Como o PHP monta uma consulta SQL;
- Como uma entrada fornecida pelo usuário pode alterar a lógica da consulta;
- Por que consultas construídas diretamente com dados do usuário são perigosas;
- Como a vulnerabilidade pode ser corrigida utilizando Prepared Statements.

## 🛠️ Tecnologias utilizadas

- PHP
- MySQL
- PDO
- HTML
- XAMPP
- phpMyAdmin

## 📥 1. Baixar o projeto

Clone o repositório utilizando o Git:

```bash
git clone URL_DO_REPOSITORIO
```

Ou faça o download do projeto pelo GitHub:

**Code → Download ZIP**

Depois, coloque a pasta do projeto dentro de:

```
C:\xampp\htdocs\
```

Por exemplo:

```
C:\xampp\htdocs\SQLInjection\
```

## ▶️ 2. Iniciar o XAMPP

1. Abra o XAMPP Control Panel.
2. Inicie os seguintes serviços:
   - Apache
   - MySQL

Os dois serviços precisam estar em execução.

## 🗄️ 3. Criar o banco de dados

Abra o phpMyAdmin no navegador:

```
http://localhost/phpmyadmin
```

Acesse a opção **SQL** e execute:

```sql
CREATE DATABASE sistema_login CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE sistema_login;

CREATE TABLE usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    senha VARCHAR(100) NOT NULL,
    nome VARCHAR(100) NOT NULL
);

INSERT INTO usuarios (usuario, senha, nome) VALUES
('joao', '123456', 'João da Silva'),
('maria', 'abc123', 'Maria Oliveira'),
('pedro', 'senha123', 'Pedro Santos');
```

Após executar, teremos:

```
sistema_login
└── usuarios
    ├── id
    ├── usuario
    ├── senha
    └── nome
```

## ⚙️ 4. Configurar a conexão com o banco

Abra o arquivo:

```
conexao.php
```

Verifique as informações da conexão:

```php
$servidor = "localhost";
$banco = "sistema_login";
$usuario = "root";
$senha = "";
```

Caso a senha do usuário root seja diferente no seu computador, altere:

```php
$senha = "SUA_SENHA";
```

## 🌐 5. Executar o sistema

Com o Apache e o MySQL funcionando, abra o navegador e acesse:

```
http://localhost/SQLInjection/
```

Caso o formulário esteja em um arquivo específico, como `login.html`, acesse:

```
http://localhost/SQLInjection/login.html
```

## 🔐 6. Testar o login normalmente

O banco possui três usuários cadastrados.

| Usuário | Senha |
|---------|-------|
| joao    | 123456 |
| maria   | abc123 |
| pedro   | senha123 |

Faça primeiro um login utilizando uma combinação correta.

Depois, teste uma senha incorreta:

```
Usuário: joao
Senha: senhaerrada
```

Nesse caso, o sistema deverá informar que o usuário ou senha estão incorretos.

## 🔎 7. Observar a consulta SQL

A aplicação exibe a consulta SQL utilizada para realizar o login.

Por exemplo:

```
Usuário: joao
Senha: 123456
```

A aplicação poderá gerar:

```sql
SELECT * FROM usuarios WHERE usuario = 'joao' AND senha = '123456'
```

Observe o caminho dos dados:

```
FORMULÁRIO
   ↓
  PHP
   ↓
CONSULTA SQL
   ↓
 MySQL
   ↓
RESULTADO
```

Essa relação é importante para entender como ocorre o SQL Injection.

## ⚠️ 8. Simular SQL Injection

A aplicação foi criada propositalmente de forma vulnerável.

No campo **Usuário**, utilize:

```
' OR 1=1 #
```

No campo **Senha**, pode ser utilizado qualquer valor.

A aplicação poderá gerar uma consulta semelhante a:

```sql
SELECT * FROM usuarios WHERE usuario = '' OR 1=1 #' AND senha = 'qualquercoisa'
```

Observe a consulta gerada.

### Entendendo o teste

- **`'`** — Interfere no fechamento da string utilizada na consulta SQL.
- **`OR`** — Permite adicionar outra condição à consulta.
- **`1=1`** — A expressão `1=1` é verdadeira. Portanto, `OR 1=1` adiciona uma condição verdadeira à consulta.
- **`#`** — No MySQL, inicia um comentário que continua até o final da mesma linha. Assim, a parte restante da consulta deixa de ser considerada na execução.

## 💻 9. Onde está a vulnerabilidade?

O problema está na forma como a consulta SQL é construída.

Exemplo:

```php
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";
```

Os valores enviados pelo formulário são inseridos diretamente na consulta SQL. Isso permite que o usuário forneça caracteres que interfiram na estrutura do comando SQL.

## 🛡️ 10. Como corrigir?

Uma das principais formas de prevenção é utilizar **Prepared Statements**.

Em PDO:

```php
$sql = "SELECT * FROM usuarios WHERE usuario = ? AND senha = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario, $senha]);
```

Nesse modelo, os valores fornecidos pelo usuário são tratados como dados, e não como parte da estrutura da consulta SQL.

## 🔄 11. Comparação

### ❌ Consulta vulnerável

```php
$sql = "SELECT * FROM usuarios WHERE usuario = '$usuario' AND senha = '$senha'";
$resultado = $pdo->query($sql);
```

O valor fornecido pelo usuário é colocado diretamente dentro da consulta.

### ✅ Consulta utilizando Prepared Statement

```php
$sql = "SELECT * FROM usuarios WHERE usuario = ? AND senha = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario, $senha]);
```

A consulta e os dados são tratados separadamente.

## 🧪 12. Atividade

Após realizar o laboratório, responda:

1. O que é SQL Injection?
2. Por que a consulta utilizada pelo sistema é vulnerável?
3. Qual é a função do `OR 1=1`?
4. Qual é a função do `#` no teste realizado?
5. O que acontece quando o usuário consegue alterar a lógica da consulta SQL?
6. Qual é a principal forma de prevenção contra SQL Injection?
7. Qual é a diferença entre uma consulta SQL vulnerável e uma consulta utilizando Prepared Statements?

## 🔐 13. Boas práticas de segurança

Em aplicações reais:

- Não insira diretamente dados do usuário em consultas SQL;
- Utilize Prepared Statements;
- Valide os dados recebidos;
- Nunca confie diretamente em informações enviadas pelo usuário;
- Utilize `password_hash()` para armazenar senhas;
- Utilize `password_verify()` para verificar senhas;
- Não exiba consultas SQL ou mensagens internas do banco para usuários;
- Utilize contas de banco de dados com apenas as permissões necessárias.

## ⚠️ Aviso importante

Este projeto contém uma vulnerabilidade intencionalmente criada para fins didáticos.

O código vulnerável **não deve** ser utilizado em sistemas reais.

Os testes devem ser realizados somente:

- No ambiente local;
- Em sistemas criados para treinamento;
- Em laboratórios autorizados.

**Nunca utilize técnicas de SQL Injection contra sistemas, sites ou aplicações sem autorização.**

## 📖 Referências

Para aprofundar os estudos sobre SQL Injection, consulte a [PortSwigger Web Security Academy](https://portswigger.net/web-security).

O conteúdo aborda conceitos, identificação de vulnerabilidades, exploração em laboratórios controlados e técnicas de prevenção.

## 👨‍🏫 Finalidade

Este projeto faz parte de uma atividade prática de Segurança de Software, permitindo relacionar os conceitos estudados em sala de aula com uma aplicação PHP e um banco de dados MySQL.

O laboratório segue o fluxo:

```
Formulário → PHP → SQL → MySQL → Resultado
```

A partir desse fluxo, é possível visualizar como uma entrada não tratada pode afetar uma consulta SQL e como Prepared Statements ajudam a evitar esse problema.
