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

## Carrinho e checkout

O carrinho é persistido por usuário nas tabelas `carts` e `cart_items`. Na página do produto o cliente escolhe uma variação e quantidade; o servidor valida produto ativo, variação ativa e saldo disponível. A tela `/carrinho` permite atualizar ou remover itens e exibe o total.

Em `/checkout`, o sistema salva ou atualiza o endereço, cria o pedido com snapshot dos itens, registra pagamento e cria o envio. Cartão, Pix e boleto funcionam somente no modo sandbox: cartão é aprovado para teste; Pix e boleto ficam pendentes. Nenhum dado de cartão é armazenado.

O checkout bloqueia as variações dentro da transação. Pagamentos pendentes reservam estoque; pagamentos aprovados baixam o saldo. A confirmação real, estorno e liberação de reservas serão responsabilidade do webhook do gateway escolhido para produção.

## Estoque

O saldo disponível é `stock - reserved_stock`. O dashboard lista variações ativas cujo estoque total é igual ou menor que `low_stock_threshold`.

## Dados de demonstração

`DatabaseSeeder` cria um catálogo local e determinístico por meio de `BrandSeeder`, `CategorySeeder`, `ProductSeeder` e `AdminUserSeeder`. Ele não baixa arquivos da internet nem combina fontes de catálogo diferentes.

`Database\Seeders\Development\RemoteCatalogSeeder` é uma ferramenta opt-in para experimentar um catálogo que baixa imagens externas. Não a execute em produção nem junto do seeder padrão, pois ele representa uma fonte de demonstração diferente.

Para executá-lo exclusivamente em um ambiente descartável já migrado:

```bash
php artisan db:seed --class="Database\Seeders\Development\RemoteCatalogSeeder"
```

Em ambiente descartável, para recriar somente os dados de demonstração padrão, use:

```bash
php artisan migrate:fresh --seed
```
