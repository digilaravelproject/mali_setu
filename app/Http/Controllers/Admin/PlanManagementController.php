<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BusinessPlan;
use App\Models\MatrimonyPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PlanManagementController extends Controller
{
    // Business plans list
    public function businessIndex(Request $request)
    {
        $plans = BusinessPlan::orderBy('company_type')->orderBy('duration_years')->get();
        return view('admin.plans.business.index', compact('plans'));
    }

    // Show create form
    public function createBusiness()
    {
        return view('admin.plans.business.create');
    }

    // Store new plan
    public function storeBusiness(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_type' => 'required|string|max:255',
            'duration_years' => 'required|integer|min:1|max:10',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        BusinessPlan::create($validator->validated());

        return redirect()->route('admin.plans.business.index')->with('success', 'Business plan created');
    }

    // Edit form
    public function editBusiness($id)
    {
        $plan = BusinessPlan::findOrFail($id);
        return view('admin.plans.business.edit', compact('plan'));
    }

    // Update plan
    public function updateBusiness(Request $request, $id)
    {
        $plan = BusinessPlan::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'company_type' => 'required|string|max:255',
            'duration_years' => 'required|integer|min:1|max:10',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $plan->update($validator->validated());

        return redirect()->route('admin.plans.business.index')->with('success', 'Business plan updated');
    }

    // Delete plan
    public function destroyBusiness($id)
    {
        $plan = BusinessPlan::findOrFail($id);
        $plan->delete();
        return redirect()->route('admin.plans.business.index')->with('success', 'Business plan deleted');
    }

    // --- Matrimony plans
    public function matrimonyIndex(Request $request)
    {
        $plans = MatrimonyPlan::orderBy('duration_years')->get();
        return view('admin.plans.matrimony.index', compact('plans'));
    }

    public function createMatrimony()
    {
        return view('admin.plans.matrimony.create');
    }

    public function storeMatrimony(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_name' => 'nullable|string|max:255',
            'duration_years' => 'required|integer|min:1|max:10',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        MatrimonyPlan::create($validator->validated());
        return redirect()->route('admin.plans.matrimony.index')->with('success', 'Matrimony plan created');
    }

    public function editMatrimony($id)
    {
        $plan = MatrimonyPlan::findOrFail($id);
        return view('admin.plans.matrimony.edit', compact('plan'));
    }

    public function updateMatrimony(Request $request, $id)
    {
        $plan = MatrimonyPlan::findOrFail($id);
        $validator = Validator::make($request->all(), [
            'plan_name' => 'nullable|string|max:255',
            'duration_years' => 'required|integer|min:1|max:10',
            'price' => 'required|numeric|min:0',
            'active' => 'boolean'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $plan->update($validator->validated());
        return redirect()->route('admin.plans.matrimony.index')->with('success', 'Matrimony plan updated');
    }

    public function destroyMatrimony($id)
    {
        $plan = MatrimonyPlan::findOrFail($id);
        $plan->delete();
        return redirect()->route('admin.plans.matrimony.index')->with('success', 'Matrimony plan deleted');
    }
}
