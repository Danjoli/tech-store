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
