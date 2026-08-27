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

## Arquivos enviados

As imagens são gravadas no disco público do Laravel. O link `public/storage` deve existir e a pasta `storage/app/public` precisa ser persistente e gravável pelo usuário do servidor.

## Fila

Não há Job obrigatório hoje. Quando Jobs forem adicionados, rode um worker supervisionado (`php artisan queue:work`) no ambiente de produção e configure o driver de fila apropriado.

## Pagamentos

Por padrão, `PAYMENTS_DRIVER=sandbox` e `PAYMENTS_MODE=sandbox`. Pix e boleto são criados como pendentes de teste; cartão é aprovado somente como simulação. Nenhum valor é cobrado e nenhum dado de cartão é armazenado.

Antes de produção, escolha um gateway, mantenha as credenciais apenas no `.env`, implemente a comunicação do provedor e valide assinaturas de webhook. Não altere `PAYMENTS_MODE` para produção sem esses itens.
