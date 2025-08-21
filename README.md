# 📋 Desafio Técnico: Desenvolvedor Pleno Full Stack (Laravel/Vue)

## 🎯 Contexto
Você deve implementar uma **área de atendimento** que permita gerenciar conversas com contatos através de diferentes canais de comunicação (WhatsApp, Messenger, Email).  
O sistema deve ser funcional, bem estruturado e demonstrar suas habilidades técnicas.

## 📦 Stack Obrigatória
- **Backend:** Laravel 12
- **Frontend:** Vue 3 (Composition API) + Inertia.js + Tailwind CSS
- **Banco de Dados:** MySQL ou SQLite

## 🔹 Backend (Laravel)

### Requisitos
- Criar estrutura para enviar e receber mensagens por diferentes canais (WhatsApp, Messenger, Email)
- Cada canal deve ter sua própria lógica de envio, mesmo que simulada
- **Persistir o histórico de mensagens** no banco de dados com relacionamentos apropriados
- Implementar **delays realistas** nas simulações (1-3 segundos) para parecer envio real
- Adicionar **tratamento de erros e logs** para falhas de envio
- Criar comando Artisan `messages:generate` que gera mensagens fake automaticamente
- Não é necessário criar endpoints REST, usaremos **Inertia** para integração

## 🔹 Frontend (Vue 3 + Inertia + Tailwind)

### Layout
Interface com duas colunas principais:
- **Lateral esquerda:** 
  - Lista de contatos com nome e preview da última mensagem
  - Indicador de mensagens não lidas
  
- **Área principal (direita):**
  - Histórico de conversas com **paginação ou scroll virtual** para performance
  - Agrupamento de mensagens por data
  - **Feedback visual** de status (enviando, enviado, erro)
  - Timestamps nas mensagens

### Funcionalidades
- Caixa de envio com seletor de canal (WhatsApp, Messenger, Email)
- **Indicadores visuais** durante o envio (loading spinner, desabilitar botão)
- **Feedback de erro** em caso de falha
- **Polling otimizado** com Inertia para novas mensagens

Para o design, você pode usar **Tailwind** e se apoiar em ferramentas como [v0](https://v0.dev) ou [Lovable](https://lovable.dev). A estética não será avaliada, mas a funcionalidade sim.

## 📋 Funcionalidades Obrigatórias

1. ✅ Listar contatos com preview da última mensagem
2. ✅ Exibir histórico completo de mensagens do contato selecionado
3. ✅ Enviar mensagens escolhendo o canal
4. ✅ Persistir todas as mensagens no banco
5. ✅ Atualizar conversas via polling
6. ✅ Tratamento visual de estados (enviando, erro, sucesso)
7. ✅ Paginação ou virtualização do histórico

## 🎯 Critérios de Avaliação

- **Estruturação do código** no backend (organização, clareza, separação de responsabilidades)
- **Solução para múltiplos canais** (extensibilidade, manutenibilidade)
- **Qualidade da interface** em Vue (componentização, reatividade)
- **Performance** (paginação, eager loading, queries otimizadas)
- **Tratamento de erros** e experiência do usuário
- **Clareza dos commits** (usar Conventional Commits)
- **Documentação** para executar o projeto

## 💡 Diferenciais (Não obrigatórios)

### Backend
- Arquitetura modular
- Testes automatizados com Pest PHP
- Migrations e seeders bem estruturados
- Jobs para processamento assíncrono

### Frontend
- Gerenciamento de estado com **Pinia**
- Componentes Vue reutilizáveis
- Modo dark/light
- Animações e transições suaves
- Acessibilidade (ARIA labels, navegação por teclado)

## 📝 Instruções de Entrega

1. Faça um **fork** deste repositório
2. Desenvolva em uma branch com seu nome
3. Faça commits frequentes e descritivos
4. Ao finalizar, abra um **Pull Request**
5. Inclua no PR qualquer observação sobre decisões tomadas

## 📚 README do Projeto

Seu projeto deve incluir um README com:
- Instruções de instalação e configuração
- Como rodar migrations e seeders
- Como executar o comando de geração de mensagens
- Como rodar os testes (se implementados)
- Decisões técnicas e trade-offs
- Possíveis melhorias futuras

## 📌 Observações Importantes

- **Não integre com APIs reais** - todas as integrações devem ser simuladas
- **Não implemente autenticação** - assuma usuário já logado
- **Foque na qualidade** em vez da quantidade de features

## ❓ Dúvidas

Entre em contato através do email fornecido ou abra uma issue neste repositório.

---

Boa sorte! 🚀