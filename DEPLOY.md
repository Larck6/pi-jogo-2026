# Publicação do Lendas do Vale

## Requisitos

- PHP 8.2 ou superior com `mysqli`, `mysqlnd`, `fileinfo` e sessões.
- MySQL 8 ou MariaDB 10.6 ou superior.
- HTTPS obrigatório em produção.
- Apache com `mod_headers` e leitura de `.htaccess`, ou regras equivalentes no Nginx.

## Banco de dados

Para uma instalação nova, crie ou selecione o banco pelo painel da hospedagem e importe `db/production_schema.sql` dentro dele. O script não tenta criar o banco, porque hospedagens compartilhadas normalmente não permitem essa operação. Não publique a pasta `db` na raiz pública se o painel permitir colocá-la fora de `public_html`.

Para atualizar o banco criado pela versão anterior do projeto, faça um backup e execute uma única vez `db/migrate_existing.sql`.

Crie um usuário exclusivo para o site. Não use `root` em produção e conceda acesso somente ao banco da aplicação.

## Configuração

Configure as variáveis mostradas em `.env.example` no painel da hospedagem. `APP_BASE_PATH` deve ficar vazio quando o domínio aponta diretamente para o projeto. Use `/pi` apenas quando o site estiver em uma subpasta chamada `pi`.

Defina também `GAME_DOWNLOAD_FILE` com o nome do instalador existente dentro da pasta `jogo`, por exemplo `Lendas-do-Vale.zip`.

## Arquivos e permissões

O processo do PHP precisa de escrita somente na pasta `uploads`. Em Linux, use normalmente permissão `0755` ou `0775`, conforme o usuário do servidor. Nunca use `0777` em produção.

As pastas de upload bloqueiam scripts pelo `.htaccess`. Em Nginx, crie uma regra equivalente que negue PHP e listagem de diretório em `/uploads`.

## Produção

- Defina `APP_ENV=production`.
- Force redirecionamento HTTP para HTTPS no painel da hospedagem.
- Desative `display_errors` e ative `log_errors` no PHP.
- Faça backup do banco e da pasta `uploads` antes de cada atualização.
- Remova do pacote público `a fazer.txt`, `style-preview.html`, `db` e qualquer arquivo de desenvolvimento.

## Validação após publicar

Teste cadastro, login, logout, troca e exclusão de conta; envio/edição/exclusão de captura e vídeo; comentários e notas; todas as páginas administrativas; download do jogo; URLs em celular; e restauração de um backup.
