# Operação do catálogo

## Estrutura

Um produto pertence a uma marca e categoria, possui uma ou mais variações e pode ter várias imagens e especificações. A variação padrão define o preço exibido no card do catálogo.

## Publicação

- **Rascunho:** salvo apenas para administração.
- **Ativo:** público quando possui `published_at` e ao menos uma variação ativa.
- **Inativo:** fora da vitrine, preservado para histórico interno.

O painel permite criar, editar e apagar marcas, categorias e produtos. Depois de criar um produto, use as telas de variações e imagens para complementar o cadastro. Defina uma imagem principal para uma apresentação consistente na vitrine.

## Favoritos

Usuários autenticados podem salvar ou remover produtos pelos corações da vitrine e da tela de detalhes. Os itens ficam na página `/favoritos` e são vinculados ao usuário na tabela `product_favorites`; não se confundem com carrinho ou pedido.

## Perfil

Em `/perfil`, o cliente pode atualizar nome, e-mail e telefone, trocar a senha e acessar os favoritos. Alterações de e-mail invalidam a confirmação anterior e disparam uma nova verificação, conforme o fluxo do Laravel Fortify.

## Estoque

O saldo disponível é `stock - reserved_stock`. O dashboard lista variações ativas cujo estoque total é igual ou menor que `low_stock_threshold`.

## Dados de demonstração

`DatabaseSeeder` chama `TechStoreCatalogSeeder`, que cria o catálogo de exemplo. Em ambiente que pode receber dados reais, não execute seeders indiscriminadamente. Para refazer somente dados de demonstração em ambiente descartável use:

```bash
php artisan migrate:fresh --seed
```
