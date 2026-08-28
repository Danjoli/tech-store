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

Também configure `SESSION_SECURE_COOKIE=true`, `SESSION_HTTP_ONLY=true` e `SESSION_SAME_SITE=lax`. Após editar o ambiente, execute `php artisan optimize:clear` e `php artisan config:cache`.

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
