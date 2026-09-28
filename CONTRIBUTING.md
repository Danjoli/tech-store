# Contribuindo com a Tech Store

O projeto usa um fluxo simples baseado em Issues, branches curtas e Pull Requests.

## Fluxo de trabalho

1. Crie ou selecione uma Issue com objetivo e critérios de aceitação.
2. Crie uma branch a partir de `main`.
3. Faça commits pequenos e relacionados ao mesmo objetivo.
4. Execute as verificações locais.
5. Abra um Pull Request ligado à Issue.
6. Aguarde a verificação automática antes do merge.

## Nome das branches

- `feat/numero-da-issue-resumo`
- `fix/numero-da-issue-resumo`
- `docs/numero-da-issue-resumo`
- `refactor/numero-da-issue-resumo`
- `test/numero-da-issue-resumo`
- `chore/numero-da-issue-resumo`

Exemplo: `feat/42-filtro-por-marca`.

## Mensagens de commit

Use `tipo: descrição curta`, com verbo no presente e um único assunto por commit.

- `feat:` nova funcionalidade
- `fix:` correção de defeito
- `docs:` documentação
- `style:` formatação sem mudança de comportamento
- `refactor:` reorganização sem nova funcionalidade
- `test:` testes
- `build:` dependências ou compilação
- `ci:` automação do GitHub
- `chore:` manutenção geral
- `security:` proteções e correções de segurança

Exemplos:

```text
feat: adiciona filtro por marca
fix: impede compra acima do estoque
test: cobre reserva de estoque no checkout
```

Evite mensagens vagas como `ajustes`, `correção`, `teste` ou `atualização`.

## Verificações locais

```bash
vendor/bin/pint --test
php artisan test
npm run typecheck
npm run build
composer audit
```

## Segurança

Nunca versione `.env`, credenciais, tokens, chaves privadas ou dados reais de clientes. Use valores fictícios em exemplos e remova informações sensíveis de logs e capturas de tela.
