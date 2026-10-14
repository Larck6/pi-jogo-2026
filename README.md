# Projeto Integrador - Portal do Jogo (PI)

Portal web interativo desenvolvido em PHP e MySQL para a comunidade de jogadores, com suporte a publicações, fórum, envio de feedbacks com notas, galeria de vídeos e capturas de tela, além de painel administrativo para moderação.

---

## 🛠️ Tecnologias Utilizadas

- **Linguagem Backend:** PHP
- **Banco de Dados:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Ícones:** Font Awesome 4.7.0

---

## 📁 Estrutura de Pastas

```text
pi/
├── css/              # Folhas de estilo (style.css)
├── db/               # Script de banco de dados (game_db.sql)
├── img/              # Imagens estáticas do site
├── include/          # Componentes reutilizáveis (header, footer, conexão, login)
├── js/               # Scripts JavaScript (carrossel, modais)
├── pages/            # Telas do sistema (login, cadastro, discussões, adm)
├── uploads/          # Diretório para armazenamento de uploads (imagens/vídeos)
├── videos/           # Mídias de vídeo
├── index.php         # Página inicial
└── README.md         # Documentação do projeto
```

---

## 🚀 Como Executar o Projeto Localmente

### 1. Pré-requisitos
- Ter um servidor local com PHP e MySQL instalado (ex: **XAMPP**, **WampServer** ou **Laragon**).

### 2. Configurar o Banco de Dados
1. Abra o painel do seu servidor local e inicie os serviços **Apache** e **MySQL**.
2. Acesse o **phpMyAdmin** no seu navegador (`http://localhost/phpmyadmin`).
3. Crie um banco de dados chamado `game_db` ou vá na aba **Importar**.
4. Selecione o arquivo `db/game_db.sql` e execute a importação.

### 3. Configurar a Conexão
Verifique o arquivo `include/conexao.php`:
```php
$host = "localhost";
$user = "root";
$pass = "";
$name = "game_db";
```
Ajuste usuário e senha caso o seu ambiente utilize credenciais diferentes.

### 4. Acessar a Aplicação
Copie esta pasta para dentro do diretório público do servidor (ex: `htdocs/pi` no XAMPP) e acesse:
```text
http://localhost/pi/index.php
```
