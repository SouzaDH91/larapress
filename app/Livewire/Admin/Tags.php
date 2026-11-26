<?php

declare(strict_types=1);

namespace App\Livewire\Admin;

use App\Models\Term;
use App\Models\TermTaxonomy;
use Illuminate\Support\Str;
use Livewire\Component;

class Tags extends Component
{
    public string $name = '';
    public string $slug = '';
    public string $description = '';

    public function updatedName($value)
    {
        if (empty($this->slug)) {
            $this->slug = Str::slug($value);
        }
    }

    public function save()
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
            'taxonomy' => 'post_tag',
        ]);

        $this->reset(['name', 'slug', 'description']);
        session()->flash('success', 'Tag criada com sucesso!');
    }

    public function delete(string $taxonomyId)
    {
        $tax = TermTaxonomy::with('term')->findOrFail($taxonomyId);
        $tax->term?->delete();
        $tax->delete();
        session()->flash('success', 'Tag excluída com sucesso!');
    }

    public function render()
    {
        $tags = TermTaxonomy::where('taxonomy', 'post_tag')->with('term')->latest()->get();

        return view('livewire.admin.tags', [
            'tags' => $tags,
        ])->layout('admin.layouts.master');
    }
}
