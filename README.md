# Tech Store

E-commerce de tecnologia construído com Laravel 12, Inertia e Vue 3. O projeto oferece vitrine pública responsiva, conta do cliente e painel administrativo para gerir catálogo, pedidos, pagamentos de teste e envios.

> Este é o guia de entrada do projeto. Consulte o [índice da documentação](docs/README.md) para arquitetura, catálogo, implantação e decisões operacionais.

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

Também é possível validar os tipos do front-end:

```bash
npm run typecheck
```

## Segurança e publicação

Antes de publicar, configure no `.env` do servidor:

```dotenv
APP_ENV=production
APP_DEBUG=false
LOG_LEVEL=warning
SESSION_SECURE_COOKIE=true
SESSION_HTTP_ONLY=true
SESSION_SAME_SITE=lax
```

Depois, execute:

```bash
php artisan optimize:clear
php artisan config:cache
```

O projeto aplica políticas de acesso para pedidos e itens do carrinho, limites de requisições nas ações comerciais e cabeçalhos de segurança do navegador. HTTPS, backup, monitoramento e um worker de fila supervisionado continuam sendo responsabilidades da infraestrutura.

## Documentação

- [Índice da documentação](docs/README.md)
- [Arquitetura e convenções](docs/ARQUITETURA.md)
- [Operação do catálogo e checkout](docs/CATALOGO.md)
- [Implantação](docs/DEPLOY.md)

## Escopo atual

O catálogo, favoritos, perfil, carrinho, checkout e gestão de pedidos/envios estão implementados. O pagamento e o frete são intencionalmente sandbox: uma cobrança ou cotação real só deve ser ativada depois de integrar, configurar e homologar os provedores escolhidos.
