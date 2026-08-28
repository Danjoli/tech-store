# Arquitetura

## Visão geral

A Tech Store usa Laravel 12 no back-end e Inertia + Vue 3 no front-end. A mesma aplicação entrega a vitrine em `resources/js/Pages/Store` e o painel em `resources/js/Pages/Admin`, sem duplicar API ou regras de autorização.

`resources/js/app.ts` é o único ponto de entrada do cliente: importa o CSS e o bootstrap HTTP, resolve páginas sob demanda via Vite e monta o Vue com o plugin do Inertia. Páginas e layouts não devem iniciar aplicações Vue próprias.

## Rotas

`routes/web.php` é somente o ponto de composição da loja. Os módulos ficam em `routes/store` por capacidade (`catalog`, `favorites`, `profile`, `cart`, `checkout` e `orders`). `routes/admin.php` aplica o middleware comum do painel e carrega `routes/admin/catalog.php`, `orders.php` e `shipments.php`.

Essa divisão preserva URLs e nomes de rota, evita arquivos extensos e não cria uma hierarquia artificial: cada módulo corresponde a um domínio de navegação ou operação.

## Camadas

- `app/Http/Controllers/Store`: controla a leitura do catálogo público.
- `app/Http/Controllers/Admin`: mantém controllers finos para a operação administrativa.
- `app/Actions/Admin/Catalog/SaveProductAction`: grava produto e sua variação padrão em uma única transação.
- `app/Services/Store/ProductCardPresenter`: centraliza o contrato de dados dos cards públicos.
- `app/Services/Admin/DashboardMetricsService`: concentra as métricas exibidas no dashboard.
- `app/Http/Controllers/Store/FavoriteController`: mantém os favoritos por usuário autenticado.
- `app/Http/Controllers/Store/ProfileController`: entrega a conta do cliente e delega a alteração de e-mail ao fluxo seguro do Fortify.
- `app/Actions/Store/Cart`: concentra inclusão e atualização de itens com validação de estoque.
- `app/Actions/Store/Checkout/CreateOrderFromCartAction`: cria endereço, pedido, itens, pagamento e envio de forma transacional.
- `app/Services/Payments`: encapsula o contrato de gateway. O sandbox é a única implementação ativa; um driver diferente falha de forma explícita até que o provedor real seja implementado.
- `app/Services/Store/OrderStockService`: reserva estoque para pagamentos pendentes e baixa unidades quando um pagamento é aprovado.
- `app/Services/Store/ShippingQuoteService`: concentra a cotação sandbox configurável, pronta para receber um provedor real.
- `app/Enums`: representa estados fechados, como `ProductStatus` e `UserRole`, com rótulos em português.
- `app/Models`: relacionamentos e regras pequenas, como preço atual e estoque disponível da variação.

## Convenções

Use `FormRequest` para validar escrita, `Action` para um caso de uso transacional e `Service` para leitura ou apresentação reutilizável. Não crie Jobs ou Observers apenas por organização: eles entram quando houver uma operação lenta, assíncrona ou um efeito lateral realmente necessário.

## Filas

O projeto já suporta fila pelo Laravel. No estado atual, o catálogo não possui trabalho assíncrono obrigatório. Quando houver importação de catálogo, otimização de imagens, e-mails ou integrações externas, esses fluxos devem ser Jobs com falha tratada e monitorada.

## Segurança e acesso

As rotas administrativas exigem autenticação, e-mail verificado e o middleware `admin`. A role é um enum (`admin` ou `customer`). Produtos públicos usam o escopo `active`, que exige status ativo e data de publicação.

Pedidos e itens do carrinho usam Policies para garantir que somente o dono consiga consultá-los ou alterá-los. Ações comerciais exigem e-mail verificado e possuem rate limiting. Contas inativas são impedidas tanto no login quanto em sessões existentes. Cadastros e redefinições de senha exigem ao menos 12 caracteres, maiúscula, minúscula, número, símbolo e verificação contra senhas expostas.

`SecurityHeadersMiddleware` adiciona CSP sem scripts inline, proteção contra MIME sniffing, clickjacking, referer excessivo, permissões de navegador desnecessárias e isolamento cross-origin. Em conexões HTTPS, também emite HSTS para manter acessos futuros no canal seguro. Uploads administrativos aceitam apenas JPG, PNG e WebP, com limite de tamanho e resolução.

### Limites da aplicação

O código reduz riscos conhecidos, mas não substitui controles de infraestrutura. A publicação segura exige HTTPS com redirecionamento ativo, `APP_DEBUG=false`, cookies seguros, um banco com credenciais exclusivas, atualizações de dependências, backups testados, permissões restritas em `storage` e monitoramento de falhas. Gateway e frete reais continuam sujeitos à homologação e à validação de webhooks autenticados antes de atender clientes.

## Pagamentos, frete e estoque

O checkout mantém uma transação única: bloqueia as variações compradas, recalcula o saldo disponível, grava o snapshot do pedido, cria a tentativa de pagamento e cria o envio. No sandbox, cartão é aprovado para teste e baixa o estoque; Pix e boleto ficam pendentes e reservam unidades em `reserved_stock`.

Em produção, a confirmação ou recusa do pagamento deve vir de um webhook autenticado do gateway. Essa integração ainda depende da escolha do provedor; não basta alterar `PAYMENTS_DRIVER` ou `PAYMENTS_MODE` no `.env`.

## Testes

`tests/Feature` é organizado por fronteira do sistema: `Admin`, `Store`, `Database` e `Http`. Esses cenários usam SQLite em memória e cobrem rotas, persistência, seeders, autorização, administração de pedidos/envios, perfil, rate limits, cabeçalhos e fluxos de compra. `tests/Unit` cobre regras e contratos focados, sem banco ou HTTP, como os gateways de pagamento e a cotação sandbox de frete.

Na última validação local, a suíte possui **88 testes e 393 asserções**. A contagem muda naturalmente quando novos cenários são acrescentados; o comando de referência é sempre `php artisan test`.

Os testes verificam o contrato local do Pix, boleto, cartão e frete em modo sandbox. Antes de uma integração de produção, a equipe deve homologar o provedor escolhido em ambiente próprio: credenciais, callbacks/webhooks autenticados, recusas, estornos, prazos e cálculo real de frete não podem ser considerados cobertos pelo sandbox.
