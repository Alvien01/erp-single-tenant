<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ProductCategory;
use App\Models\ActivityLog;
use Illuminate\Support\Str;

class CategoryManager extends Component
{
    use WithPagination;

    public $search = '';
    
    // Form fields
    public $category_id;
    public $name;
    public $slug;
    public $description;

    public $isOpen = false;
    public $isEditMode = false;

    // Listeners or properties
    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'nullable|string|max:255',
        'description' => 'nullable|string',
    ];

    public function updatedName($value)
    {
        if (!$this->isEditMode) {
            $this->slug = Str::slug($value);
        }
    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->category_id = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->isEditMode = false;
        $this->resetErrorBag();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function store()
    {
        $this->validate();

        // Auto-generate slug if empty
        $finalSlug = $this->slug ?: Str::slug($this->name);

        // Check unique slug within the tenant's product categories
        $query = ProductCategory::where('slug', $finalSlug);
        if ($this->category_id) {
            $query->where('id', '!=', $this->category_id);
        }
        if ($query->exists()) {
            $finalSlug .= '-' . Str::random(4);
        }

        ProductCategory::updateOrCreate(['id' => $this->category_id], [
            'name' => $this->name,
            'slug' => $finalSlug,
            'description' => $this->description,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => $this->isEditMode ? 'Update Category' : 'Create Category',
            'description' => 'Category ' . $this->name . ' has been saved.'
        ]);

        session()->flash('success', 'Category saved successfully.');
        $this->closeModal();
    }

    public function edit($id)
    {
        $category = ProductCategory::findOrFail($id);
        $this->category_id = $category->id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function delete($id)
    {
        $category = ProductCategory::findOrFail($id);

        // Check if there are any products attached to this category
        if ($category->products()->exists()) {
            session()->flash('error', 'Cannot delete category "' . $category->name . '" because it is linked to ' . $category->products()->count() . ' products.');
            return;
        }
        
        ActivityLog::create([
            'user_id' => auth()->id() ?? 1,
            'module' => 'Master Data',
            'action' => 'Delete Category',
            'description' => 'Category ' . $category->name . ' has been deleted.'
        ]);

        $category->delete();
        session()->flash('success', 'Category deleted successfully.');
    }

    public function render()
    {
        $query = ProductCategory::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('slug', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.category-manager', [
            'categories' => $query->orderBy('name')->paginate(10),
        ])->layout('layouts.app');
    }
}
