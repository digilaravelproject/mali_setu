<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHomepageHeroRequest;
use App\Http\Requests\UpdateHomepageHeroRequest;
use App\Models\HomepageHero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HomepageHeroController extends Controller
{
    // You can protect these routes with middleware like ->middleware('auth','can:manage-heroes') in routes

    public function index(Request $request)
    {
        $heroes = HomepageHero::latest()->paginate(10);
        return view('admin.heroes.index', compact('heroes'));
    }

    public function create()
    {
        return view('admin.heroes.create');
    }

    public function store(StoreHomepageHeroRequest $request)
    {
        $path = $request->file('image')->store('hero_images', 'public');

        $hero = HomepageHero::create([
            'title' => $request->title,
            'image_path' => $path,
        ]);

        return redirect()
            ->route('heroes.index')
            ->with('success', 'Hero created successfully.');
    }

    public function show(HomepageHero $hero)
    {
        return view('admin.heroes.show', compact('hero'));
    }

    public function edit(HomepageHero $hero)
    {
        return view('admin.heroes.edit', compact('hero'));
    }

    public function update(UpdateHomepageHeroRequest $request, HomepageHero $hero)
    {
        $data = ['title' => $request->title];

        if ($request->hasFile('image')) {
            // delete old image
            if ($hero->image_path && Storage::disk('public')->exists($hero->image_path)) {
                Storage::disk('public')->delete($hero->image_path);
            }
            $data['image_path'] = $request->file('image')->store('hero_images', 'public');
        }

        $hero->update($data);

        return redirect()
            ->route('heroes.index')
            ->with('success', 'Hero updated successfully.');
    }

    public function destroy(HomepageHero $hero)
    {
        if ($hero->image_path && Storage::disk('public')->exists($hero->image_path)) {
            Storage::disk('public')->delete($hero->image_path);
        }

        $hero->delete();

        return redirect()
            ->route('heroes.index')
            ->with('success', 'Hero deleted successfully.');
    }
}
