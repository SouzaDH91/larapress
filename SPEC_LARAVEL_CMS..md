# Especificação Técnica e Levantamento de Requisitos: LaraPress CMS

## 1. Visão Geral do Projeto

O **LaraPress CMS** é um Sistema de Gerenciamento de Conteúdo (CMS) construído sobre o ecossistema **Laravel 13**, projetado para oferecer toda a flexibilidade, extensibilidade e facilidade de uso do WordPress (posts, páginas, mídias, menus, temas e plugins), mas eliminando suas vulnerabilidades históricas através de práticas modernas de engenharia de software, tipagem estrita, isolamento de código e segurança nativa do Laravel.

## 2. Pilares de Segurança (O Diferencial)

Para resolver os problemas de invasão e infecção comuns no WordPress, o sistema deve seguir rigorosamente:

1. **Zero Execução Dinâmica Insegura:** Proibição absoluta de `eval()` ou execução de código PHP arbitrário vindo do banco de dados.
2. **Chaves Primárias em UUID (Anti-Enumeração):** Todas as tabelas do sistema utilizam UUIDs (`HasUuids` do Eloquent) como chave primária, impedindo ataques de enumeração sequencial de usuários, posts, mídias e registros internos.
3. **Isolamento de Uploads:** Arquivos enviados via mídia não podem ter permissão de execução no servidor web (Nginx/Apache). Validação rigorosa de MIME-type real (Magic Bytes) e renomeação de arquivos via UUID.
4. **Sistema de Plugins Baseado em Pacotes/Módulos Estruturados:** Plugins não são scripts soltos; são Service Providers do Laravel com manifestos (`plugin.json`) validados, rodando sob permissões declaradas e registrando suas próprias views Blade e componentes Livewire sem necessidade de recompilação de assets.
5. **Proteção Nativa:** Uso obrigatório de CSRF Tokens, Eloquent ORM (prevenção de SQL Injection), Blade Escaping `{{ }}` (prevenção de XSS), Rate Limiting no login/APIs e Headers de Segurança (CSP, HSTS, X-Frame-Options).
6. **Autenticação Moderna:** Suporte nativo a 2FA (Autenticação de Dois Fatores), bloqueio por força bruta e logs de auditoria de ações administrativas.

## 3. Levantamento Completo de Requisitos

### 3.1. Requisitos Funcionais (RF) - Tudo que o WordPress faz

#### A. Gestão de Conteúdo (Core)

* **RF01 - Posts (Artigos):** Criação, edição, exclusão (Soft Delete / Lixeira), agendamento de publicação, status (Rascunho, Pendente, Publicado, Privado, Protegido por Senha), posts fixos (Sticky) e revisões (histórico de alterações com opção de restaurar).
* **RF02 - Páginas:** Criação de páginas estáticas com suporte a hierarquia (Página Pai / Página Filha) e seleção de templates específicos do tema (ex: `page-contato.blade.php`).
* **RF03 - Custom Post Types (CPT):** Capacidade do núcleo ou de plugins registrarem novos tipos de conteúdo (ex: Portfólio, Produtos, Eventos) reutilizando a mesma estrutura de banco.
* **RF04 - Taxonomias:**
  * *Categorias:* Hierárquicas (pai/filho), com slug, descrição e imagem destacada.
  * *Tags:* Não hierárquicas.
  * *Custom Taxonomies:* Suporte para taxonomias personalizadas vinculadas a CPTs.
* **RF05 - Campos Personalizados (Custom Fields / Meta Data):** Estrutura chave-valor (Post Meta, User Meta, Term Meta) para armazenar dados extras sem alterar o schema principal.
* **RF06 - Editor de Conteúdo:** Editor visual moderno baseado em blocos (estilo Gutenberg, ex: Editor.js ou TipTap integrado via Alpine.js/Livewire) salvando em JSON estruturado + renderização HTML limpa.

#### B. Biblioteca de Mídia

* **RF07 - Upload e Gestão:** Upload múltiplo (Drag & Drop via Livewire File Uploads), suporte a imagens, vídeos, áudios e documentos (PDF, DOCX).
* **RF08 - Processamento de Imagens:** Geração automática de miniaturas (Thumbnail, Medium, Large) e conversão automática para **WebP** no momento do upload (via Intervention Image), além de edição básica (cortar, redimensionar).
* **RF09 - Metadados de Mídia:** Edição de Texto Alternativo (`alt`), Título, Legenda e Descrição. Organização por pastas virtuais ou Ano/Mês.

#### C. Aparência e Temas (Frontend Público)

* **RF10 - Motor de Temas:** Capacidade de instalar temas via upload de `.zip` ou diretório `/themes`. Cada tema possui um `theme.json` com metadados (nome, versão, autor, print, áreas de menu, áreas de widgets) e utiliza **Blade** para máxima compatibilidade de SEO/SSR.
* **RF11 - Customizer (Personalizador):** Interface reativa em Livewire no painel administrativo para alterar identidade do site (Logo, Favicon, Título, Slogan), cores, tipografia e configurações específicas expostas pelo tema ativo, com preview em tempo real via iframe.
* **RF12 - Menus de Navegação:** Construtor de menus drag-and-drop (Alpine.js + SortableJS + Livewire) suportando links personalizados, páginas, posts e categorias, com suporte a submenus multinível e atribuição a locais do tema (Header, Footer, Sidebar).
* **RF13 - Widgets / Áreas Dinâmicas:** Blocos reutilizáveis (ex: Posts Recentes, Barra de Pesquisa, HTML Personalizado) que podem ser arrastados para áreas registradas pelo tema.

#### D. Plugins e Extensibilidade

* **RF14 - Gerenciador de Plugins:** Instalar (via `.zip` ou Composer), ativar, desativar e desinstalar plugins.
* **RF15 - Arquitetura de Hooks (Actions & Filters) e UI Dinâmica:** Implementação de um sistema de Eventos/Hooks estilo WordPress (`add_action`, `do_action`, `add_filter`, `apply_filters`) integrado aos Events do Laravel. Plugins podem registrar namespaces de views Blade (`View::addNamespace`), componentes Livewire (`Livewire::component`) e injetar itens de menu na sidebar do Velzon instantaneamente ao serem ativados, sem exigir Node.js ou build step em produção.
* **RF16 - Shortcodes:** Parser para tags curtas no conteúdo (ex: `[galeria id="uuid"]`) que executam classes/funções registradas por plugins.

#### E. Usuários e Permissões (ACL)

* **RF17 - Gestão de Usuários:** Cadastro, edição de perfil, avatar, biografia e redefinição de senha.
* **RF18 - Papéis e Capacidades (Roles & Capabilities):** Replicação exata dos níveis do WordPress (Administrador, Editor, Autor, Colaborador, Assinante) usando `spatie/laravel-permission` adaptado para UUIDs, permitindo granularidade (ex: `edit_posts`, `publish_posts`, `manage_options`).

#### F. Interação, SEO e Configurações

* **RF19 - Sistema de Comentários:** Comentários aninhados (respostas), moderação (Aprovado, Pendente, Spam, Lixeira), filtro anti-spam (Honeypot + Turnstile/reCAPTCHA) e opção de fechar comentários por post.
* **RF20 - Links Permanentes (Permalinks):** Configuração dinâmica da estrutura de URLs (ex: `/%year%/%month%/%postname%/` ou `/%postname%/`) com redirecionamento automático 301 em caso de mudança de slug.
* **RF21 - SEO Nativo & Feeds:** Geração automática de `sitemap.xml`, `robots.txt` editável, RSS Feed (`/feed`) e meta tags OpenGraph/Twitter Cards.
* **RF22 - Configurações Gerais:** Fuso horário, formato de data/hora, idioma do painel, modo de manutenção, configurações de leitura (Página inicial estática vs Posts recentes) e escrita.
* **RF23 - API RESTful:** Endpoints JSON públicos e autenticados (via Laravel Sanctum) para uso Headless (consumo por apps mobile ou frontends SPA).

### 3.2. Requisitos Não Funcionais (RNF)

* **RNF01 - Stack Tecnológica:**
  * **Backend:** PHP 8.3+ (ou 8.4/8.5), **Laravel 13**, MySQL 8.0+ ou PostgreSQL 16+, Redis (Cache, Sessões e Filas).
  * **Painel Administrativo (Backoffice):** **Laravel Blade + Livewire (v3/v4) + Alpine.js + Bootstrap 5 (última versão)** estruturado sobre o template **Velzon (versão Laravel/HTML)**.
  * **Frontend Público (Temas):** Laravel Blade (para renderização rápida, SEO nativo e facilidade de criação de temas).
* **RNF02 - Performance:** Cache de página inteira (Full-Page Cache) para visitantes não logados, invalidação inteligente de cache ao atualizar posts/menus e *Eager Loading* obrigatório para evitar queries N+1.
* **RNF03 - Logs e Auditoria:** Registro imutável de quem criou, editou ou deletou conteúdos, instalou plugins ou falhou tentativas de login.

## 4. Arquitetura do Banco de Dados (Schema Principal com UUIDs)

**Regra Global de Schema:** Todas as colunas `id` (Primary Key) e `*_id` (Foreign Keys) devem ser do tipo `uuid()` (`CHAR(36)` no MySQL ou `UUID` nativo no PostgreSQL). Todos os Models Eloquent devem utilizar a trait `Illuminate\Database\Eloquent\Concerns\HasUuids`.

* `users` (id \[uuid\], name, email, password, username, bio, avatar_id \[uuid nullable\], two_factor_secret, status, timestamps)
* `posts` (id \[uuid\], author_id \[uuid\], parent_id \[uuid nullable\], title, slug, excerpt, content_json, content_html, status, type \[post, page, cpt\], password, comment_status, ping_status, menu_order, featured_media_id \[uuid nullable\], published_at, deleted_at, timestamps)
* `post_meta` (id \[uuid\], post_id \[uuid\], meta_key, meta_value \[JSON/LongText\], index em meta_key)
* `post_revisions` (id \[uuid\], post_id \[uuid\], user_id \[uuid\], title, content_json, created_at)
* `terms` (id \[uuid\], name, slug, description, media_id \[uuid nullable\])
* `term_taxonomies` (id \[uuid\], term_id \[uuid\], taxonomy \[category, post_tag, custom\], parent_id \[uuid nullable\], count)
* `post_term` (post_id \[uuid\], term_taxonomy_id \[uuid\])
* `media` (id \[uuid\], user_id \[uuid\], filename, disk, path, mime_type, size, alt_text, title, caption, metadata_json, timestamps)
* `comments` (id \[uuid\], post_id \[uuid\], user_id \[uuid nullable\], parent_id \[uuid nullable\], author_name, author_email, author_ip, content, status \[approved, pending, spam, trash\], timestamps)
* `options` (id \[uuid\], option_name \[unique\], option_value \[JSON/LongText\], autoload \[boolean\])
* `menus` (id \[uuid\], name, location_slug)
* `menu_items` (id \[uuid\], menu_id \[uuid\], parent_id \[uuid nullable\], title, url, linkable_type, linkable_id \[uuid nullable\], target, css_class, order)
* `plugins` (id \[uuid\], slug \[unique\], name, version, is_active, settings_json, timestamps)
* `activity_logs` (id \[uuid\], user_id \[uuid nullable\], action, subject_type, subject_id \[uuid nullable\], ip_address, user_agent, properties_json, created_at)

## 5. Plano de Execução para o Agente Desenvolvedor (Roadmap)

Siga rigorosamente a ordem das fases abaixo para construir o sistema sem gerar débito técnico.

### Fase 1: Setup Laravel 13, Integração Velzon (Blade + Livewire) e Segurança Base

* [ ] **Passo 1.1:** Inicializar projeto **Laravel 13** com suporte a filas (Redis) e configurar variáveis de ambiente estritas. Alterar a migration padrão de `users` e `sessions` para suportar `uuid` na chave primária e `user_id`.
* [ ] **Passo 1.2:** Instalar **Livewire** e integrar a estrutura base do template **Velzon** em Blade (`resources/views/admin/layouts/master.blade.php`, sidebar dinâmica com hooks, topbar, dark/light mode, modais, toasts e estilos Bootstrap 5 compilados via Vite). *Nota: O desenvolvedor possui os arquivos do Velzon localmente para importação dos assets SCSS/JS e layouts Blade.*
* [ ] **Passo 1.3:** Instalar e configurar `spatie/laravel-permission` habilitando suporte a UUIDs nas migrations (`model_uuid` e PKs das tabelas `roles` e `permissions`). Criar Seeder com os 5 papéis padrão do WordPress e suas respectivas permissões granulares.
* [ ] **Passo 1.4:** Implementar sistema de Autenticação utilizando as telas de Auth do Velzon (Login, Recuperação de Senha, 2FA TOTP) com proteção contra Brute Force (RateLimiter) e página de perfil de usuário (`user_meta`) em Livewire.
* [ ] **Passo 1.5:** Criar middleware de segurança HTTP (CSP, X-Frame-Options, XSS Protection), View Composers para injetar menus administrativos dinâmicos na sidebar do Velzon e sistema de Auditoria (`activity_logs`) via Observers do Eloquent.

### Fase 2: Núcleo de Configurações e Mídia Segura

* [ ] **Passo 2.1:** Criar tabela `options` (com UUID) e um serviço global (`OptionService` / helpers `get_option()` e `update_option()`) com cache em Redis (`autoload = true`). Criar componentes Livewire para as telas de Configurações no painel Velzon.
* [ ] **Passo 2.2:** Construir o módulo de Mídia (`MediaService`). Implementar validação estrita de MIME-type via assinatura binária (Magic Bytes), bloqueio de extensões executáveis (`.php`, `.sh`, `.exe`, `.svg` sem sanitização), geração de thumbnails/WebP e componente Livewire modal/biblioteca visual estilo Velzon File Manager (`wire:navigate` e uploads múltiplos).

### Fase 3: Motor de Conteúdo, Taxonomias e Revisões

* [ ] **Passo 3.1:** Criar migrations (todas com UUIDs) e models (com `HasUuids`) para `posts`, `post_meta` e `post_revisions`.
* [ ] **Passo 3.2:** Implementar CRUD completo em Livewire (com tabelas paginadas, busca em tempo real e ações em lote) de **Páginas** (com hierarquia) e **Posts** (com agendamento via Laravel Scheduler, rascunhos, lixeira e restauração de revisões).
* [ ] **Passo 3.3:** Integrar editor de blocos (TipTap ou Editor.js via Alpine.js + `wire:ignore`) com suporte a inserção direta de itens do modal Livewire da Biblioteca de Mídia.
* [ ] **Passo 3.4:** Implementar sistema de Taxonomias (`categories` hierárquicas e `tags`) e vinculação polimórfica aos posts com componentes Livewire de criação/seleção rápida no editor.

### Fase 4: Arquitetura de Extensibilidade (Hooks, Plugins e Temas)

* [ ] **Passo 4.1:** Desenvolver o `HookManager` (Singleton registrado no AppServiceProvider) provendo as funções globais `add_action`, `do_action`, `add_filter` e `apply_filters`, além do `ShortcodeParser`.
* [ ] **Passo 4.2:** Criar a estrutura de diretórios `/themes` e `/plugins` na raiz (ou dentro de `storage/app/extensions` com symlinks controlados).
* [ ] **Passo 4.3:** Desenvolver o `ThemeManager`: leitura de `theme.json`, registro dinâmico de views no Blade (`View::addNamespace('theme', ...)`), suporte a templates de página e tela de Customizer reativa em Livewire com preview em iframe.
* [ ] **Passo 4.4:** Desenvolver o `PluginManager`: leitura de `plugin.json`, carregamento dinâmico do `ServiceProvider` do plugin apenas se `is_active = true`, registro automático de views Blade (`View::addNamespace('plugin-slug', ...)`) e componentes Livewire (`Livewire::component(...)`), suporte para plugins injetarem itens na sidebar do Velzon via hooks e rotina segura de extração de arquivos `.zip` (verificando estrutura e proibindo arquivos maliciosos antes de extrair).

### Fase 5: Menus, Widgets, Comentários e Roteamento Dinâmico

* [ ] **Passo 5.1:** Criar construtor de Menus em Livewire + Alpine.js/SortableJS (drag-and-drop com cards estilo Velzon) e diretiva Blade `@menu('header')` para renderização nos temas.
* [ ] **Passo 5.2:** Criar sistema de Widgets e áreas dinâmicas (`@dynamic_sidebar('footer-1')`).
* [ ] **Passo 5.3:** Implementar sistema de Comentários aninhados com fila de moderação reativa em Livewire no painel Velzon e proteção anti-spam.
* [ ] **Passo 5.4:** Construir o **Roteador Dinâmico (Permalink Engine)** no final do `routes/web.php` (Fallback/Catch-all route) que resolve URLs dinâmicas baseadas na configuração de Permalinks (resolvendo Pages, Posts, Categorias, Tags e Arquivos por data) e dispara os hooks de renderização do tema ativo.

### Fase 6: SEO, API Headless e Otimização Final

* [ ] **Passo 6.1:** Criar geradores dinâmicos para `/sitemap.xml`, `/robots.txt` e `/feed` (RSS 2.0).
* [ ] **Passo 6.2:** Criar endpoints RESTful (`/api/v1/posts`, `/api/v1/pages`, `/api/v1/media`) protegidos por Sanctum.
* [ ] **Passo 6.3:** Implementar middleware de Full-Page Cache para usuários deslogados com limpeza automática ao salvar/editar conteúdo.
* [ ] **Passo 6.4:** Criar tema padrão inicial ("LaraTwentySix") funcional demonstrando o uso de menus, widgets, loop de posts, single post, página e comentários.

## 6. Diretrizes de Código para o Agente

1. **Padrão de Projeto:** Utilize *Service Classes* para regras de negócio, *Form Objects* do Livewire (ou Form Requests) para validações de entrada e componentes Livewire focados em responsabilidade única.
2. **UUIDs Obrigatórios:** Nunca utilize `$table->id()` ou `$table->foreignId()`. Utilize sempre `$table->uuid('id')->primary()` e `$table->foreignUuid('...')`.
3. **Tipagem:** Declare `declare(strict_types=1);` em todos os arquivos PHP e utilize type-hints em todos os métodos e propriedades.
4. **Isolamento de Assets Admin vs Tema:** Os assets do painel Velzon (Bootstrap 5, ícones Remix/Boxicons, JS do template) devem ser carregados exclusivamente no layout administrativo (`/admin/*`), nunca vazando para o layout dos temas públicos.
5. **Testes:** Escreva testes automatizados (Pest ou PHPUnit) cobrindo especialmente: upload de arquivos maliciosos (garantindo bloqueio), controle de acesso por papel (ACL) com UUIDs, ativação/desativação de plugins e resolução de rotas dinâmicas.