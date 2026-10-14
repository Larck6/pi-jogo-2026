# Contexto do Projeto Integrador (PI 2026)

Este documento foi gerado para fornecer contexto técnico completo sobre o projeto para desenvolvedores e agentes de Inteligência Artificial.

---

## 1. Visão Geral do Projeto
* **Nome:** Portal da Comunidade do Jogo (PI 2026)
* **Objetivo:** Plataforma web para divulgação, suporte, comunidade e download de um jogo desenvolvido como Projeto Integrador.
* **Stack Tecnológico:**
  * **Backend:** PHP 8.x (estruturado com `mysqli` e controle de sessão nativo)
  * **Banco de Dados:** MySQL / MariaDB (relacional com Foreign Keys em cascata)
  * **Frontend:** HTML5 semântico, CSS3 e JavaScript puro (Vanilla JS)
  * **Servidor Web:** Apache (WampServer localmente e Alwaysdata na nuvem)

---

## 2. Estrutura de Diretórios
```text
pi/
├── .gitignore                      # Regras de exclusão do Git
├── README.md                       # Apresentação do projeto no GitHub
├── contexto_IA_projeto_PI_2026.md  # Este arquivo de contexto para IA
├── a fazer.txt                     # Backlog e anotações dos alunos
├── index.php                       # Página inicial (Home com carrossel)
├── css/
│   └── style.css                   # Folha de estilos unificada
├── db/
│   ├── game_db.sql                 # Script SQL original
│   └── importar_nuvem.sql          # Script SQL compatível com servidores em nuvem
├── img/                            # Imagens estáticas e uploads de capturas
├── include/
│   ├── conexao.php                 # Conexão híbrida (Local + Nuvem) e constantes
│   ├── header.php                  # Cabeçalho e navegação principal
│   ├── footer.php                  # Rodapé e fechamento das páginas
│   ├── id.php                      # Leitura e sanitização das variáveis de sessão
│   ├── login.php                   # Modal / formulário visual de login
│   ├── login_php.php               # Processamento do formulário de login/logout
│   ├── comunidade_menu.php         # Menu lateral/secundário da comunidade
│   └── menu_adm.php                # Menu exclusivo para administradores
├── jogo/                           # Arquivos para download do jogo
├── js/
│   ├── login.js                    # Abertura e fechamento do modal de login
│   └── slide.js                    # Controle do carrossel de imagens da home
├── pages/                          # Telas públicas, de usuários e administrativas
│   ├── cadastro.php                # Cadastro de novos usuários
│   ├── login.php                   # Página dedicada de login
│   ├── sobre.php                   # Sobre o projeto/jogo
│   ├── download.php                # Download protegido do executável do jogo
│   ├── tudo.php / tudo_ADM.php     # Visão geral da comunidade e visão ADM
│   ├── capturas.php                # Galeria de capturas de tela e feedbacks
│   ├── videos.php                  # Galeria de vídeos e feedbacks
│   ├── discussoes.php              # Fórum de discussões e tópicos
│   ├── feedback.php                # Feedbacks gerais do jogo
│   ├── mecanicas.php               # Exibição de mecânicas do jogo
│   └── atualizacoes.php            # Patch notes e novidades
├── uploads/                        # Pastas para arquivos dinâmicos (mecânicas e atualizações)
└── videos/                         # Mídias e vídeos da comunidade
```

---

## 3. Banco de Dados e Conexão Híbrida

### Tabelas (`game_db`):
1. **`user`**: `pk_id_user`, `name_user`, `email_user`, `password_user`, `quant_feedback`, `adm`
2. **`feedback`**: Feedbacks textuais gerais com notas.
3. **`imagens`**: Capturas de tela enviadas por usuários.
4. **`feedback_imagem`**: Notas (1 a 5) e comentários em imagens.
5. **`videos`**: Vídeos enviados pela comunidade.
6. **`feedback_video`**: Notas (1 a 5) e comentários em vídeos.
7. **`forum`**: Tópicos de discussão.
8. **`forum_comentario`**: Respostas nos tópicos do fórum.
9. **`mecanicas`**: Imagens e textos de mecânicas do jogo.
10. **`atualizacoes`**: Patch notes e logs de atualizações.

### Conexão Híbrida (`include/conexao.php`):
* **Localmente:** Se o host for `localhost` ou `127.0.0.1` e o WampServer estiver rodando, conecta automaticamente ao MySQL local (`localhost`, usuário `root`, senha vazia, base `game_db`).
* **Nuvem / Fallback:** Se não for local ou se o banco local não estiver disponível, conecta automaticamente ao banco na nuvem:
  * **Host:** `mysql-pijogo2026.alwaysdata.net`
  * **Usuário:** `pijogo2026`
  * **Banco:** `pijogo2026_game_db`
* **Usuário Administrador inicial de testes:**
  * **E-mail:** `admin@teste.com`
  * **Senha:** `admin123`

---

## 4. Ambientes de Execução

### Ambiente Local (Desenvolvimento):
* **Diretório do projeto:** `D:\Ano3\Site PI 2026\pi (1) (1)\pi`
* **Integração WampServer:** Junção criada em `C:\wamp64\www\pi` apontando para o projeto.
* **URL Local:** `http://localhost/pi/index.php`

### Ambiente Online (Produção / Demonstração):
* **Provedor:** Alwaysdata (Hospedagem Web + MySQL)
* **URL Pública:** `http://pijogo2026.alwaysdata.net`
* **Diretório no servidor:** `/home/pijogo2026/www/` com atalho simbólico `pi -> .` para compatibilidade de rotas.

### Repositório GitHub:
* **URL:** `https://github.com/Larck6/pi-jogo-2026`
* **Branch principal:** `main`

---

## 5. Fluxo de Trabalho (Deploy)
1. Fazer as alterações e testar em `http://localhost/pi/index.php`.
2. Commitar e subir para o GitHub:
   ```bash
   git add .
   git commit -m "Mensagem da alteração"
   git push origin main
   ```
3. Atualizar o site em produção via terminal SSH do Alwaysdata (`on the Web`):
   ```bash
   git clone https://github.com/Larck6/pi-jogo-2026.git site && cp -rf site/* www/ && rm -rf site
   ```

---

## 6. Oportunidades Futuras de Melhoria
* Implementar hash de senha (`password_hash` / `password_verify`).
* Substituir consultas SQL concatenadas por **Prepared Statements** contra SQL Injection.
* Validação rigorosa de extensões e tipos MIME nos formulários de upload.
