<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Test;
use App\Models\TestCategory;
use App\Models\TestPackage;
use App\Services\LabAudit;
use Illuminate\Http\Request;

class CatalogController extends Controller
{
    public function tests(Request $request)
    {
        $catId = $request->get('category_id');
        $tests = Test::with(['category', 'department', 'parameters'])
            ->when($catId, fn ($q) => $q->where('category_id', $catId))
            ->orderBy('code')
            ->paginate(15);

        $categories = TestCategory::withCount('tests')->get();
        $departments = Department::orderBy('name')->get();

        return view('catalog.tests', compact('tests', 'categories', 'departments', 'catId'));
    }

    public function storeTest(Request $request)
    {
        $data = $request->validate([
            'category_id' => 'required|exists:test_categories,id',
            'department_id' => 'required|exists:departments,id',
            'code' => 'required|string|unique:tests,code',
            'name' => 'required|string|max:255',
            'sample_type' => 'required|string',
            'container_type' => 'required|string',
            'sample_quantity' => 'required|string',
            'tat_hours' => 'required|integer|min:1',
            'methodology' => 'required|string',
            'price' => 'required|numeric|min:0',
            'preparation_instructions' => 'nullable|string',
        ]);
        $data['fasting_required'] = $request->boolean('fasting_required');
        $data['branch_availability'] = [1, 2, 3];

        $test = Test::create($data);
        LabAudit::log('Created Test', 'Test Master', $test->code, $test->name);

        return back()->with('success', "Test {$test->code} ({$test->name}) added to master catalog.");
    }

    public function packages()
    {
        $packages = TestPackage::with('tests')->latest()->get();
        $tests = Test::where('is_active', true)->orderBy('name')->get();

        return view('catalog.packages', compact('packages', 'tests'));
    }

    public function storePackage(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'price' => 'required|numeric|min:0',
            'discount_percent' => 'required|numeric|min:0|max:90',
            'test_ids' => 'required|array|min:1',
            'description' => 'nullable|string',
        ]);

        $finalPrice = round($data['price'] * (1 - $data['discount_percent'] / 100), 2);
        $pkg = TestPackage::create([
            'code' => 'PKG-' . str_pad((string) ((TestPackage::max('id') ?? 0) + 1), 3, '0', STR_PAD_LEFT),
            'name' => $data['name'],
            'category' => $data['category'],
            'description' => $data['description'] ?? null,
            'price' => $data['price'],
            'discount_percent' => $data['discount_percent'],
            'final_price' => $finalPrice,
            'is_corporate' => $data['category'] === 'Corporate',
        ]);
        $pkg->tests()->sync($data['test_ids']);

        LabAudit::log('Created Package', 'Packages', $pkg->code, $pkg->name);

        return back()->with('success', "Package {$pkg->name} created at ₹{$finalPrice}.");
    }
}

