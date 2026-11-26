<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Menu;
use App\Models\MenuItem;
use App\Models\Post;
use App\Models\TermTaxonomy;
use Livewire\Component;

final class Menus extends Component
{
    // Modos de visualização: 'list' (listagem de menus) ou 'builder' (construtor de itens)
    public string $viewMode = 'builder';

    // Gerenciamento de Menu (CRUD)
    public ?string $selectedMenuId = null;
    public string $menuName = '';
    public string $locationSlug = 'header';
    public bool $isEditingMenu = false;
    public ?string $editingMenuId = null;

    // Adição de Links Personalizados
    public string $customLinkTitle = '';
    public string $customLinkUrl = '';

    // Adição de Páginas/Posts/Categorias em lote
    public array $selectedPages = [];
    public array $selectedPosts = [];
    public array $selectedCategories = [];

    // Edição inline de item existente
    public ?string $editingItemId = null;
    public string $editItemTitle = '';
    public string $editItemUrl = '';
    public string $editItemTarget = '_self';
    public string $editItemCssClass = '';

    public function mount(string|null $menuId = null): void
    {
        if ($menuId) {
            $this->selectedMenuId = $menuId;
            $this->viewMode = 'builder';
        } else {
            $firstMenu = Menu::first();
            if ($firstMenu) {
                $this->selectedMenuId = $firstMenu->id;
                $this->menuName = $firstMenu->name;
                $this->locationSlug = $firstMenu->location_slug ?? 'header';
            }
        }
    }

    public function selectMenu(string $id): void
    {
        $this->selectedMenuId = $id;
        $menu = Menu::find($id);
        if ($menu) {
            $this->menuName = $menu->name;
            $this->locationSlug = $menu->location_slug ?? 'header';
        }
        $this->viewMode = 'builder';
        $this->isEditingMenu = false;
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
        if ($mode === 'create') {
            $this->selectedMenuId = null;
            $this->menuName = '';
            $this->locationSlug = 'header';
        }
    }

    // Salvar ou Criar Menu
    public function saveMenu(): void
    {
        $this->validate([
            'menuName' => 'required|string|max:255',
            'locationSlug' => 'required|string|max:50',
        ]);

        if ($this->selectedMenuId) {
            $menu = Menu::findOrFail($this->selectedMenuId);
            $menu->update([
                'name' => $this->menuName,
                'location_slug' => $this->locationSlug,
            ]);
            session()->flash('success', "Menu '{$menu->name}' atualizado com sucesso!");
        } else {
            $menu = Menu::create([
                'name' => $this->menuName,
                'location_slug' => $this->locationSlug,
            ]);
            $this->selectedMenuId = $menu->id;
            $this->viewMode = 'builder';
            session()->flash('success', "Menu '{$menu->name}' criado com sucesso! Agora adicione os links.");
        }
    }

    // Excluir Menu
    public function deleteMenu(string $id): void
    {
        $menu = Menu::findOrFail($id);
        $name = $menu->name;
        $menu->delete();

        $nextMenu = Menu::first();
        $this->selectedMenuId = $nextMenu?->id;
        if ($nextMenu) {
            $this->menuName = $nextMenu->name;
            $this->locationSlug = $nextMenu->location_slug ?? 'header';
        } else {
            $this->menuName = '';
        }

        session()->flash('success', "Menu '{$name}' excluído com sucesso!");
    }

    // Adicionar Link Personalizado
    public function addCustomLink(): void
    {
        if (!$this->selectedMenuId) {
            session()->flash('error', 'Selecione ou crie um menu primeiro.');
            return;
        }

        $this->validate([
            'customLinkTitle' => 'required|string|max:255',
            'customLinkUrl' => 'required|string|max:255',
        ]);

        $maxOrder = MenuItem::where('menu_id', $this->selectedMenuId)->max('order') ?? 0;

        MenuItem::create([
            'menu_id' => $this->selectedMenuId,
            'title' => $this->customLinkTitle,
            'url' => $this->customLinkUrl,
            'order' => $maxOrder + 1,
            'target' => '_self',
        ]);

        $this->reset(['customLinkTitle', 'customLinkUrl']);
        session()->flash('success', 'Link adicionado ao menu!');
    }

    // Adicionar Páginas Selecionadas
    public function addSelectedPages(): void
    {
        if (!$this->selectedMenuId || empty($this->selectedPages)) {
            return;
        }

        $pages = Post::where('type', 'page')->whereIn('id', $this->selectedPages)->get();
        $maxOrder = MenuItem::where('menu_id', $this->selectedMenuId)->max('order') ?? 0;

        foreach ($pages as $page) {
            $maxOrder++;
            MenuItem::create([
                'menu_id' => $this->selectedMenuId,
                'title' => $page->title,
                'url' => '/' . $page->slug,
                'linkable_type' => Post::class,
                'linkable_id' => $page->id,
                'order' => $maxOrder,
            ]);
        }

        $this->selectedPages = [];
        session()->flash('success', 'Páginas adicionadas ao menu!');
    }

    // Adicionar Posts Selecionados
    public function addSelectedPosts(): void
    {
        if (!$this->selectedMenuId || empty($this->selectedPosts)) {
            return;
        }

        $posts = Post::where('type', 'post')->whereIn('id', $this->selectedPosts)->get();
        $maxOrder = MenuItem::where('menu_id', $this->selectedMenuId)->max('order') ?? 0;

        foreach ($posts as $post) {
            $maxOrder++;
            MenuItem::create([
                'menu_id' => $this->selectedMenuId,
                'title' => $post->title,
                'url' => '/' . $post->slug,
                'linkable_type' => Post::class,
                'linkable_id' => $post->id,
                'order' => $maxOrder,
            ]);
        }

        $this->selectedPosts = [];
        session()->flash('success', 'Artigos adicionados ao menu!');
    }

    // Adicionar Categorias Selecionadas
    public function addSelectedCategories(): void
    {
        if (!$this->selectedMenuId || empty($this->selectedCategories)) {
            return;
        }

        $taxonomies = TermTaxonomy::where('taxonomy', 'category')
            ->whereIn('id', $this->selectedCategories)
            ->with('term')
            ->get();

        $maxOrder = MenuItem::where('menu_id', $this->selectedMenuId)->max('order') ?? 0;

        foreach ($taxonomies as $tax) {
            if (!$tax->term) continue;
            $maxOrder++;
            MenuItem::create([
                'menu_id' => $this->selectedMenuId,
                'title' => $tax->term->name,
                'url' => '/category/' . $tax->term->slug,
                'linkable_type' => TermTaxonomy::class,
                'linkable_id' => $tax->id,
                'order' => $maxOrder,
            ]);
        }

        $this->selectedCategories = [];
        session()->flash('success', 'Categorias adicionadas ao menu!');
    }

    // Mover Item para cima ou para baixo
    public function moveItem(string $itemId, string $direction): void
    {
        $item = MenuItem::findOrFail($itemId);
        $sibling = $direction === 'up'
            ? MenuItem::where('menu_id', $item->menu_id)->where('order', '<', $item->order)->orderBy('order', 'desc')->first()
            : MenuItem::where('menu_id', $item->menu_id)->where('order', '>', $item->order)->orderBy('order', 'asc')->first();

        if ($sibling) {
            $currentOrder = $item->order;
            $item->update(['order' => $sibling->order]);
            $sibling->update(['order' => $currentOrder]);
        }
    }

    // Iniciar edição de item
    public function editItem(string $itemId): void
    {
        $item = MenuItem::findOrFail($itemId);
        $this->editingItemId = $item->id;
        $this->editItemTitle = $item->title;
        $this->editItemUrl = $item->url ?? '';
        $this->editItemTarget = $item->target ?? '_self';
        $this->editItemCssClass = $item->css_class ?? '';
    }

    public function updateItem(): void
    {
        if (!$this->editingItemId) return;

        $this->validate([
            'editItemTitle' => 'required|string|max:255',
            'editItemUrl' => 'required|string|max:255',
        ]);

        $item = MenuItem::findOrFail($this->editingItemId);
        $item->update([
            'title' => $this->editItemTitle,
            'url' => $this->editItemUrl,
            'target' => $this->editItemTarget,
            'css_class' => $this->editItemCssClass,
        ]);

        $this->editingItemId = null;
        session()->flash('success', 'Item de menu atualizado!');
    }

    public function cancelEditItem(): void
    {
        $this->editingItemId = null;
    }

    // Excluir Item do Menu
    public function deleteItem(string $itemId): void
    {
        MenuItem::findOrFail($itemId)->delete();
        session()->flash('success', 'Item removido do menu!');
    }

    public function render()
    {
        $menus = Menu::withCount('items')->get();
        $currentMenu = $this->selectedMenuId
            ? Menu::with(['items' => fn ($q) => $q->orderBy('order', 'asc')])->find($this->selectedMenuId)
            : null;

        $availablePages = Post::where('type', 'page')->where('status', 'published')->latest()->take(20)->get();
        $availablePosts = Post::where('type', 'post')->where('status', 'published')->latest()->take(20)->get();
        $availableCategories = TermTaxonomy::where('taxonomy', 'category')->with('term')->get();

        return view('livewire.admin.menus', [
            'menus' => $menus,
            'currentMenu' => $currentMenu,
            'availablePages' => $availablePages,
            'availablePosts' => $availablePosts,
            'availableCategories' => $availableCategories,
        ])->layout('admin.layouts.master');
    }
}
