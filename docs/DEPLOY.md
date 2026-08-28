# Implantação

## Passos essenciais

```bash
composer install --no-dev --optimize-autoloader
npm ci
npm run build
php artisan migrate --force
php artisan storage:link
php artisan optimize
```

Configure `APP_ENV=production`, `APP_DEBUG=false`, credenciais de banco e um `APP_KEY` exclusivo no `.env` do servidor. Nunca versione o `.env`.

Também configure `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true`, `SESSION_ENCRYPT=true` e `SESSION_SAME_SITE=lax`. O domínio deve servir exclusivamente por HTTPS: a aplicação emite HSTS em respostas seguras e o redirecionamento HTTP → HTTPS precisa continuar ativo na Hostinger. Após editar o ambiente, execute `php artisan optimize:clear` e `php artisan config:cache`.

Para produção, use também logs rotativos e pouco verbosos:

```dotenv
LOG_CHANNEL=stack
LOG_STACK=daily
LOG_LEVEL=warning
```

O `.env` real, `APP_KEY`, senhas de banco, SMTP, tokens de gateway e segredos de webhook não podem ir para o Git ou aparecer em logs. Mantenha backups do banco e das imagens fora do diretório público e limite o acesso SSH/painel aos administradores necessários.

## Arquivos enviados

As imagens são gravadas no disco público do Laravel. O link `public/storage` deve existir e a pasta `storage/app/public` precisa ser persistente e gravável pelo usuário do servidor.

## Fila e tarefas externas

Não há Job obrigatório hoje. Quando Jobs forem adicionados, rode um worker supervisionado (`php artisan queue:work`) no ambiente de produção e configure o driver de fila apropriado. Integrações de gateway, webhooks, e-mails e importações não devem depender de uma requisição web longa.

## Pagamentos

Por padrão, `PAYMENTS_DRIVER=sandbox` e `PAYMENTS_MODE=sandbox`. Pix e boleto são criados como pendentes de teste; cartão é aprovado somente como simulação. Nenhum valor é cobrado e nenhum dado de cartão é armazenado.

Antes de produção, escolha um gateway, mantenha as credenciais apenas no `.env`, implemente a comunicação do provedor e valide assinaturas de webhook. Não altere `PAYMENTS_MODE` para produção sem esses itens.

## Frete e assets

`SHIPPING_DRIVER=sandbox` e `SHIPPING_SANDBOX_FLAT_RATE=0` existem apenas para desenvolvimento. Antes de produção, escolha e homologue uma transportadora ou agregador, incluindo cotação, compra de etiqueta, rastreio e autenticação de webhook.

As imagens de produtos usam o disco público do Laravel. O favicon oficial é `public/favicon.svg`; mantenha-o junto da identidade visual ao publicar.
