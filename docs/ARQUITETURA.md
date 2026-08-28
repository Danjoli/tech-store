# Arquitetura

## Visão geral

A Tech Store usa Laravel 12 no back-end e Inertia + Vue 3 no front-end. A mesma aplicação entrega a vitrine em `resources/js/Pages/Store` e o painel em `resources/js/Pages/Admin`, sem duplicar API ou regras de autorização.

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

Pedidos e itens do carrinho usam Policies para garantir que somente o dono consiga consultá-los ou alterá-los. Ações comerciais exigem e-mail verificado e possuem rate limiting. `SecurityHeadersMiddleware` adiciona CSP, proteção contra MIME sniffing, clickjacking, referer excessivo e permissões de navegador desnecessárias.

## Pagamentos, frete e estoque

O checkout mantém uma transação única: bloqueia as variações compradas, recalcula o saldo disponível, grava o snapshot do pedido, cria a tentativa de pagamento e cria o envio. No sandbox, cartão é aprovado para teste e baixa o estoque; Pix e boleto ficam pendentes e reservam unidades em `reserved_stock`.

Em produção, a confirmação ou recusa do pagamento deve vir de um webhook autenticado do gateway. Essa integração ainda depende da escolha do provedor; não basta alterar `PAYMENTS_DRIVER` ou `PAYMENTS_MODE` no `.env`.
