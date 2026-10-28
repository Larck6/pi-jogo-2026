# Contexto do Projeto Integrador (PI 2026) - Lendas do Vale

Este documento foi gerado para fornecer contexto técnico completo sobre o projeto para desenvolvedores e agentes de Inteligência Artificial.

---

## 1. Visão Geral do Projeto
* **Nome:** Lendas do Vale - Portal da Comunidade (PI 2026)
* **Objetivo:** Plataforma web para divulgação, suporte, comunidade e download de um jogo desenvolvido como Projeto Integrador.
* **Stack Tecnológico:**
  * **Backend:** PHP 8.2+ com `declare(strict_types=1)`, Prepared Statements (`db_stmt`), proteção CSRF e controle de sessão seguro.
  * **Banco de Dados:** MySQL 8 / MariaDB (relacional com Foreign Keys em cascata, chaves `INT UNSIGNED`, `created_at`, charset `utf8mb4`).
  * **Frontend:** HTML5 semântico, CSS3 modular (`style_home.css`, `style_comunidade.css`, `style_login.css`, `style_sobre.css`), JavaScript moderno (`parallax.js`, `lightbox.js`, `chefes.js`).
  * **Servidor Web:** Apache (WampServer localmente e Alwaysdata na nuvem com `.htaccess`).

---

## 2. Estrutura de Diretórios (Atualizada - Mês 10)
```text
pi/
├── .env.example                    # Exemplo de variáveis de ambiente
├── .gitignore                      # Regras de exclusão do Git (blindagem de senhas)
├── .htaccess                       # Configurações de segurança e cabeçalhos HTTP
├── a fazer.txt                     # Backlog e anotações dos alunos
├── DEPLOY.md                       # Instruções de publicação dos alunos
├── README.md                       # Apresentação do projeto no GitHub
├── contexto_IA_projeto_PI_2026.md  # Este arquivo de contexto consolidado
├── credenciais_acesso_SEGREDO.txt  # Senhas e logins locais (IGNORADO PELO GIT)
├── index.php                       # Página inicial (Home reformulada)
├── style-preview.html              # Guia de estilos e componentes visuais
├── Prints/                         # Mídias e artes de bosses, fundos e scroll do jogo
├── css/
│   ├── style.css                   # Folha de estilos base
│   ├── style_home.css              # Estilos exclusivos da página inicial
│   ├── style_comunidade.css        # Estilos da área de comunidade e feedbacks
│   ├── style_login.css             # Estilos dedicados para telas de login/cadastro
│   └── style_sobre.css             # Estilos da página sobre o jogo
├── db/
│   ├── game_db.sql                 # Script SQL original
│   ├── production_schema.sql       # NOVO SCHEMA COMPLETO (Produção)
│   └── migrate_existing.sql        # Script de migração de versões antigas
├── img/                            # Imagens estáticas e assets
├── include/
│   ├── app.php                     # Funções centrais: CSRF, uploads, redirects, auth
│   ├── config.php                  # Carregamento de variáveis de ambiente e diretórios
│   ├── config.local.php            # Credenciais locais seguras (IGNORADO PELO GIT)
│   ├── conexao.php                 # Conexão híbrida resiliente (Local + Nuvem)
│   ├── header.php                  # Cabeçalho com navegação e tags dinâmicas
│   ├── footer.php                  # Rodapé padrão
│   ├── id.php                      # Sanitização e mapeamento de variáveis de sessão
│   ├── login.php                   # Modal visual de login
│   ├── login_php.php               # Processamento de autenticação com password_verify
│   ├── comunidade_menu.php         # Menu secundário da comunidade
│   └── menu_adm.php                # Menu exclusivo para administradores
├── jogo/                           # Arquivos e instalador do jogo para download
├── js/
│   ├── chefes.js                   # Interatividade da seção de chefes/bosses
│   ├── lightbox.js                 # Visualizador modal de capturas/imagens
│   ├── login.js                    # Abertura e controle do modal de login
│   ├── parallax.js                 # Efeitos de scroll parallax na Home
│   └── slide.js                    # Carrossel de imagens
├── pages/
│   ├── cadastro.php                # Cadastro seguro com hash de senha e upload de avatar
│   ├── login.php                   # Página de login com mensagem de feedback
│   ├── sobre.php                   # História, lore e personagens do jogo
│   ├── download.php                # Download protegido do executável
│   ├── tudo.php / tudo_ADM.php     # Visão geral da comunidade
│   ├── capturas.php / _ADM.php     # Galeria de capturas e moderação
│   ├── videos.php / _ADM.php       # Galeria de vídeos e moderação
│   ├── discussoes.php / _ADM.php   # Fórum de tópicos
│   ├── feedback.php / _ADM.php     # Feedbacks gerais do jogo
│   ├── mecanicas.php / _ADM.php    # Exibição de mecânicas do jogo
│   └── atualizacoes.php / _ADM.php # Patch notes e novidades
└── uploads/                        # Pastas seguras de mídias enviadas por usuários
```

---

## 3. Banco de Dados Atualizado (Mês 10)
O banco foi reconstruído a partir do **`db/production_schema.sql`** tanto no MySQL local quanto no Alwaysdata:
* Chaves primárias e estrangeiras padronizadas com `INT UNSIGNED`.
* Novas colunas: `foto_user` (VARCHAR 500) e `created_at` (TIMESTAMP) em todas as tabelas.
* Tamanho de senha expandido para `VARCHAR(255)` para suportar `PASSWORD_DEFAULT` (`password_hash`).
* Restrições únicas (`uq_user_email`, `uq_feedback_user`, `uq_feedback_imagem_user`, `uq_feedback_video_user`).
* Relacionamentos `ON DELETE CASCADE ON UPDATE CASCADE`.

### Usuário Administrador de Testes:
* **E-mail:** `admin@teste.com`
* **Senha:** `admin123`
* **Permissão:** `adm = 1`

---

## 4. Conexão Híbrida e Segurança
* **Local:** `http://localhost/pi/index.php` (WAMP em `C:\wamp64\www\pi`).
* **Nuvem:** `http://pijogo2026.alwaysdata.net`.
* As credenciais sensíveis ficam isoladas em `include/config.local.php` (no PC) e nunca sobem para o GitHub.
* Repositório oficial: **`https://github.com/Larck6/pi-jogo-2026`** (branch `main`).
