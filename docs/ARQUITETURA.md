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
- `app/Enums`: representa estados fechados, como `ProductStatus` e `UserRole`, com rótulos em português.
- `app/Models`: relacionamentos e regras pequenas, como preço atual e estoque disponível da variação.

## Convenções

Use `FormRequest` para validar escrita, `Action` para um caso de uso transacional e `Service` para leitura ou apresentação reutilizável. Não crie Jobs ou Observers apenas por organização: eles entram quando houver uma operação lenta, assíncrona ou um efeito lateral realmente necessário.

## Filas

O projeto já suporta fila pelo Laravel. No estado atual, o catálogo não possui trabalho assíncrono obrigatório. Quando houver importação de catálogo, otimização de imagens, e-mails ou integrações externas, esses fluxos devem ser Jobs com falha tratada e monitorada.

## Segurança e acesso

As rotas administrativas exigem autenticação, e-mail verificado e o middleware `admin`. A role é um enum (`admin` ou `customer`). Produtos públicos usam o escopo `active`, que exige status ativo e data de publicação.
