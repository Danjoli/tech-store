# Documentação técnica — Tech Store

Este diretório complementa o [README da raiz](../README.md). O README serve para instalação e operação rápida; estes arquivos registram as decisões e limites técnicos do sistema.

## Ordem de leitura

1. [Arquitetura](ARQUITETURA.md): camadas, organização do código, estoque, pagamentos, frete e segurança.
2. [Catálogo e checkout](CATALOGO.md): operação administrativa, favoritos, perfil, carrinho e regras de estoque.
3. [Implantação](DEPLOY.md): publicação, variáveis de ambiente, fila, pagamentos e assets.

## Manutenção

- Documente alterações em fluxos de pagamento, estoque, entrega, autenticação ou segurança na arquitetura e na implantação.
- Atualize o catálogo quando formulários, produtos, variações, imagens ou regras de compra mudarem.
- Mantenha a estrutura de rotas por domínio; novos arquivos em `routes/store` ou `routes/admin` só devem surgir quando houver uma capacidade independente.
- Mantenha testes de Feature nas áreas `Admin`, `Store`, `Database` ou `Http`; `tests/Unit` contém somente regras sem banco ou HTTP, como cálculos de modelos e contratos sandbox de gateway e frete.
- Não registre senhas, chaves, tokens, dados de clientes ou o conteúdo de um `.env` real.
- Antes de registrar uma mudança, valide com `vendor/bin/pint --test`, `php artisan test`, `npm run typecheck` e `npm run build`.
