<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Term;
use App\Models\TermTaxonomy;
use Illuminate\Support\Str;
use Livewire\Component;

final class Categories extends Component
{
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public string $taxonomy = 'category';

    public function updatedName(string $value): void
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255',
        ]);

        $term = Term::create([
            'name' => $this->name,
            'slug' => Str::slug($this->slug),
            'description' => $this->description,
        ]);

        TermTaxonomy::create([
            'term_id' => $term->id,
            'taxonomy' => $this->taxonomy,
        ]);

        $this->reset(['name', 'slug', 'description']);
        session()->flash('success', 'Categoria criada com sucesso!');
    }

    public function delete(string $taxonomyId): void
    {
        $tax = TermTaxonomy::with('term')->findOrFail($taxonomyId);
        $tax->term?->delete();
        $tax->delete();
        session()->flash('success', 'Categoria excluída!');
    }

    public function render()
    {
        $categories = TermTaxonomy::where('taxonomy', 'category')->with('term')->latest()->get();

        return view('livewire.admin.categories', [
            'categories' => $categories,
        ])->layout('admin.layouts.master');
    }
}
