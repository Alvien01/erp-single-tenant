<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Product;
use App\Models\ProductCategory;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $search = '';
    public $category_id = '';
    public $product_id;
    public $code;
    public $name;
    public $selected_category_id;
    public $price;
    public $stock;
    public $min_stock;
    public $unit = 'pcs';
    public $type = 'ready_stock';
    public $image;
    public $existing_images = [];

    public $isOpen = false;
    public $isEditMode = false;
    public $confirmingProductDeletion = false;
    public $deleteProductId = null;
    public $deleteProductName = '';
    public $deleteProductCode = '';

    protected function rules()
    {
        $rules = [
            'code'                 => 'required|unique:products,code',
            'name'                 => 'required|string|max:255',
            'selected_category_id' => 'nullable|exists:product_categories,id',
            'price'                => 'required|numeric|min:0',
            'unit'                 => 'required|string|max:20',
            'type'                 => 'required|in:ready_stock,pre_order',
            'image'                => 'nullable|image|max:5120',
        ];

        if ($this->type === 'ready_stock') {
            $rules['stock']     = 'required|numeric|min:0';
            $rules['min_stock'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function openModal()
    {
        $this->confirmingProductDeletion = false;
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
    }

    public function resetInputFields()
    {
        $this->product_id           = null;
        $this->code                 = 'PROD-' . now()->format('Ymd') . '-' . sprintf('%04d', Product::count() + 1);
        $this->name                 = '';
        $this->selected_category_id = '';
        $this->price                = '';
        $this->stock                = '';
        $this->min_stock            = '';
        $this->unit                 = 'pcs';
        $this->type                 = 'ready_stock';
        $this->image                = null;
        $this->existing_images      = [];
        $this->isEditMode           = false;
        $this->resetValidation();
    }

    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }

    public function updatedType()
    {
        if ($this->type === 'pre_order') {
            $this->stock     = 0;
            $this->min_stock = 0;
        }
        $this->resetValidation(['stock', 'min_stock']);
    }

    public function store()
    {
        $rules = $this->rules();
        if ($this->isEditMode) {
            $rules['code'] = 'required|unique:products,code,' . $this->product_id;
        }

        $this->validate($rules);

        $productData = [
            'code'        => $this->code,
            'name'        => $this->name,
            'category_id' => $this->selected_category_id ?: null,
            'price'       => $this->price,
            'stock'       => $this->type === 'ready_stock' ? $this->stock : 0,
            'min_stock'   => $this->type === 'ready_stock' ? $this->min_stock : 0,
            'unit'        => $this->unit,
            'type'        => $this->type,
        ];

        if ($this->image) {
            $manager  = ImageManager::usingDriver(Driver::class);
            $realPath = $this->image->getRealPath();
            $baseName = 'prod_' . Str::random(10) . '_' . time();
            $path     = 'products/' . $baseName;

            if (!Storage::disk('public')->exists('products')) {
                Storage::disk('public')->makeDirectory('products');
            }

            $images = [];

            // LG — 600px wide
            $imgLg = $manager->decodePath($realPath);
            $imgLg->scaleDown(width: 600);
            $images['lg'] = $path . '_lg.webp';
            Storage::disk('public')->put(
                $images['lg'],
                $imgLg->encode(new WebpEncoder(quality: 80))
            );
            $imgMd = $manager->decodePath($realPath);
            $imgMd->scaleDown(width: 300);
            $images['md'] = $path . '_md.webp';
            Storage::disk('public')->put(
                $images['md'],
                $imgMd->encode(new WebpEncoder(quality: 75))
            );
            $imgSm = $manager->decodePath($realPath);
            $imgSm->scaleDown(width: 150);
            $images['sm'] = $path . '_sm.webp';
            Storage::disk('public')->put(
                $images['sm'],
                $imgSm->encode(new WebpEncoder(quality: 70))
            );

            if ($this->isEditMode && !empty($this->existing_images)) {
                foreach ($this->existing_images as $oldPath) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $productData['image'] = $images;
        }

        Product::updateOrCreate(['id' => $this->product_id], $productData);

        session()->flash(
            'success',
            $this->isEditMode ? 'Product updated successfully.' : 'Product created successfully.'
        );

        $this->closeModal();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);

        $this->product_id           = $product->id;
        $this->code                 = $product->code;
        $this->name                 = $product->name;
        $this->selected_category_id = $product->category_id;
        $this->price                = $product->price;
        $this->type                 = $product->type ?? 'ready_stock';
        $this->stock                = $product->stock;
        $this->min_stock            = $product->min_stock;
        $this->unit                 = $product->unit;
        $this->existing_images      = $product->image ?: [];
        $this->image                = null;

        $this->isEditMode = true;
        $this->openModal();
    }

    public function confirmDelete($id)
    {
        $product = Product::find($id);
        if ($product) {
            $this->isOpen = false;
            $this->deleteProductId = $product->id;
            $this->deleteProductName = $product->name;
            $this->deleteProductCode = $product->code;
            $this->confirmingProductDeletion = true;
        }
    }

    public function cancelDelete()
    {
        $this->confirmingProductDeletion = false;
        $this->deleteProductId = null;
        $this->deleteProductName = '';
        $this->deleteProductCode = '';
    }

    public function delete($id = null)
    {
        $targetId = $id ?? $this->deleteProductId;
        if (!$targetId) {
            return;
        }

        $product = Product::find($targetId);

        if ($product) {
            if ($product->image) {
                foreach ($product->image as $path) {
                    Storage::disk('public')->delete($path);
                }
            }

            $product->delete();
            session()->flash('success', 'Product deleted successfully.');
        }

        $this->cancelDelete();
    }

    public function render()
    {
        $query = Product::select('id', 'code', 'name', 'category_id', 'price', 'stock', 'min_stock', 'unit', 'type', 'image')
            ->with(['category:id,name']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return view('livewire.product-manager', [
            'products'   => $query->orderBy('code')->paginate(10),
            'categories' => ProductCategory::select('id', 'name')->get(),
        ]);
    }
}
