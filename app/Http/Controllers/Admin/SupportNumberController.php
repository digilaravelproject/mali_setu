<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportNumberController extends Controller
{
    public function index(): View
    {
        $numbers = [
            'primary' => SystemSetting::getValue('support_number_primary', ''),
            'secondary' => SystemSetting::getValue('support_number_secondary', ''),
        ];

        return view('admin.support-numbers.index', compact('numbers'));
    }

    public function show(string $slot): View
    {
        abort_unless(in_array($slot, ['primary', 'secondary'], true), 404);
        $number = SystemSetting::getValue('support_number_'.$slot, '');

        return view('admin.support-numbers.show', compact('slot', 'number'));
    }

    public function update(Request $request, string $slot): RedirectResponse
    {
        abort_unless(in_array($slot, ['primary', 'secondary'], true), 404);
        $validated = $request->validate(['number' => ['required', 'regex:/^\+?[0-9]{8,15}$/']]);
        SystemSetting::setValue('support_number_'.$slot, $validated['number'], ucfirst($slot).' customer support number');

        return redirect()->route('admin.support-numbers.index')->with('success', 'Support number updated successfully.');
    }
}
