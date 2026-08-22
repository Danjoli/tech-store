<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBrandRequest;
use App\Http\Requests\Admin\UpdateBrandRequest;
use App\Models\Brand;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class BrandController extends Controller
{
    public function index(Request $request): Response
    {
        $search = trim($request->string('search')->toString());
        $status = $request->string('status')->toString();

        $brands = Brand::query()
            ->withCount('products')
            ->when($search, function ($query) use ($search): void {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($status === 'active', function ($query): void {
                $query->where('is_active', true);
            })
            ->when($status === 'inactive', function ($query): void {
                $query->where('is_active', false);
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString()
            ->through(fn (Brand $brand): array => [
                'id' => $brand->id,
                'name' => $brand->name,
                'slug' => $brand->slug,
                'logo_url' => $brand->logo_path
                    ? Storage::url($brand->logo_path)
                    : null,
                'website_url' => $brand->website_url,
                'is_active' => $brand->is_active,
                'products_count' => $brand->products_count,
            ]);

        return Inertia::render('Admin/Brands/Index', [
            'brands' => $brands,
            'filters' => [
                'search' => $search,
                'status' => $status,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Brands/Create');
    }

    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['logo']);
        $data['slug'] = $this->generateUniqueSlug($data['name']);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request
                ->file('logo')
                ->store('brands', 'public');
        }

        Brand::create($data);

        return to_route('admin.brands.index')
            ->with('success', 'Marca cadastrada com sucesso.');
    }

    public function show(Brand $brand): RedirectResponse
    {
        return to_route('admin.brands.edit', $brand);
    }

    public function edit(Brand $brand): Response
    {
        return Inertia::render('Admin/Brands/Edit', [
            'brand' => [
                'id' => $brand->id,
                'name' => $brand->name,
                'description' => $brand->description,
                'website_url' => $brand->website_url,
                'logo_url' => $brand->logo_path
                    ? Storage::url($brand->logo_path)
                    : null,
                'is_active' => $brand->is_active,
            ],
        ]);
    }

    public function update(
        UpdateBrandRequest $request,
        Brand $brand,
    ): RedirectResponse {
        $data = $request->safe()->except([
            'logo',
            'remove_logo',
        ]);

        if ($data['name'] !== $brand->name) {
            $data['slug'] = $this->generateUniqueSlug(
                $data['name'],
                $brand,
            );
        }

        if ($request->hasFile('logo')) {
            if ($brand->logo_path) {
                Storage::disk('public')->delete($brand->logo_path);
            }

            $data['logo_path'] = $request
                ->file('logo')
                ->store('brands', 'public');
        } elseif ($request->boolean('remove_logo')) {
            if ($brand->logo_path) {
                Storage::disk('public')->delete($brand->logo_path);
            }

            $data['logo_path'] = null;
        }

        $brand->update($data);

        return to_route('admin.brands.index')
            ->with('success', 'Marca atualizada com sucesso.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        if ($brand->products()->exists()) {
            return back()->with(
                'error',
                'Não é possível excluir uma marca que possui produtos.',
            );
        }

        $brand->delete();

        return to_route('admin.brands.index')
            ->with('success', 'Marca excluída com sucesso.');
    }

    private function generateUniqueSlug(
        string $name,
        ?Brand $ignoredBrand = null,
    ): string {
        $baseSlug = Str::slug($name) ?: 'marca';
        $slug = $baseSlug;
        $suffix = 2;

        while (
            Brand::withTrashed()
                ->where('slug', $slug)
                ->when(
                    $ignoredBrand,
                    fn ($query) => $query->whereKeyNot($ignoredBrand->id),
                )
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
