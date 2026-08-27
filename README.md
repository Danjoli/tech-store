# Tech Store

Catálogo de tecnologia construído com Laravel 12, Inertia e Vue 3. O projeto oferece uma vitrine pública responsiva e um painel administrativo para gerir marcas, categorias, produtos, variações, imagens e especificações.

## Começar localmente

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm run dev
php artisan serve
```

Use `composer dev` para subir servidor, fila, logs e Vite juntos durante o desenvolvimento.

## Qualidade

```bash
php artisan test
vendor/bin/pint
npm run build
```

Os testes usam SQLite em memória automaticamente; a aplicação pode continuar usando MySQL no ambiente local e em produção.

## Documentação

- [Arquitetura e convenções](docs/ARQUITETURA.md)
- [Operação do catálogo](docs/CATALOGO.md)
- [Implantação](docs/DEPLOY.md)

## Escopo atual

O catálogo, os favoritos, o perfil, o carrinho, o checkout e a gestão de pedidos/envios estão prontos. Pagamentos iniciam em sandbox; uma cobrança real depende da escolha e configuração de um gateway.
