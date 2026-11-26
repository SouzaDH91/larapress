# LaraPress CMS

Um Sistema de Gerenciamento de Conteúdo (CMS) moderno, modular e focado em segurança, inspirado na flexibilidade do WordPress e construído sobre a arquitetura robusta do **Laravel 13**.

## Por que o LaraPress?

O WordPress democratizou a web, mas sua arquitetura legada o torna um alvo constante de ataques, injeções de código e infecções por plugins vulneráveis. O **LaraPress CMS** nasceu para oferecer a mesma facilidade de uso (posts, páginas, mídias, menus, temas e plugins), mas protegido pelos padrões modernos de engenharia e segurança nativa do ecossistema Laravel.

### Diferenciais de Segurança
* **Zero `eval()` e Código Isolado:** Plugins funcionam como Service Providers estruturados com manifestos validados, sem execução arbitrária de scripts.
* **UUIDs em Todo o Banco:** Chaves primárias e estrangeiras baseadas estritamente em UUIDs, eliminando ataques de enumeração de IDs.
* **Upload Blindado:** Validação real de arquivos por assinatura binária (Magic Bytes), bloqueio de executáveis e conversão automática de imagens para WebP.
* **Proteção Nativa:** Prevenção contra SQL Injection (Eloquent), XSS (Blade Escaping), CSRF, Rate Limiting contra força bruta, 2FA (TOTP) e logs completos de auditoria.

---

## Principais Funcionalidades

* **Gestão de Conteúdo:** Posts, Páginas hierárquicas, Custom Post Types (CPT), agendamento, revisões e editor visual em blocos.
* **Taxonomias & Metadados:** Categorias, Tags, taxonomias customizadas e sistema flexível de Custom Fields.
* **Biblioteca de Mídia:** Upload múltiplo drag-and-drop com redimensionamento automático e conversão para WebP.
* **Motor de Temas & Customizer:** Instalação de temas via `.zip`, construtor de menus drag-and-drop, widgets e personalização visual com preview em tempo real.
* **Arquitetura de Plugins & Hooks:** Sistema nativo de `Actions`, `Filters` e `Shortcodes` estilo WordPress, permitindo estender o painel e o frontend sem recompilação de assets.
* **ACL Granular:** Controle de papéis e permissões (Administrador, Editor, Autor, Colaborador e Assinante).
* **SEO & Headless Ready:** Geração automática de `sitemap.xml`, `robots.txt`, RSS Feed e API RESTful protegida com Laravel Sanctum.

---

## Stack Tecnológica

* **Backend:** PHP 8.3+ & Laravel 13
* **Painel Administrativo:** Blade + Livewire + Alpine.js + Bootstrap 5 (Velzon)
* **Frontend Público:** Blade SSR (focado em SEO e performance)
* **Banco de Dados & Cache:** MySQL 8+ / PostgreSQL 16+ e Redis

---

## Instalação Rápida

```bash
# Clone o repositório
git clone https://github.com/seu-usuario/larapress-cms.git
cd larapress-cms

# Instale as dependências PHP e Node
composer install
npm install && npm run build

# Configure o ambiente
cp .env.example .env
php artisan key:generate

# Execute as migrations e seeders (papéis e usuário admin)
php artisan migrate --seed

# Crie o link simbólico de storage
php artisan storage:link
```

---

## Roadmap

A especificação técnica completa e o plano de desenvolvimento em fases estão documentados no arquivo [`SPEC_LARAVEL_CMS.md`](./SPEC_LARAVEL_CMS.md).

## Licença

Distribuído sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.