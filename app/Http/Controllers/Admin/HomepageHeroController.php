<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHomepageHeroRequest;
use App\Http\Requests\UpdateHomepageHeroRequest;
use App\Models\Blog;
use App\Models\Business;
use App\Models\DonationCause;
use App\Models\HomepageHero;
use App\Models\JobPosting;
use App\Models\MatrimonyProfile;
use App\Models\Product;
use App\Models\Service;
use App\Models\VolunteerOpportunity;
use App\Models\Webinar;
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
        return view('admin.heroes.create', [
            'bannerFunctionalities' => $this->bannerFunctionalities(),
        ]);
    }

    public function store(StoreHomepageHeroRequest $request)
    {
        $path = $request->file('mobile_image')->store('hero_images', 'public');
        $webPath = $request->file('web_image')->store('hero_images', 'public');

        $hero = HomepageHero::create([
            'title' => $request->title,
            'functionality_name' => $request->input('functionality_name'),
            'event_name' => $request->input('event_name'),
            'image_path' => $path,
            'web_image_path' => $webPath,
            'url' => $request->input('url'),
            'external_url' => $request->input('external_url'),
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
        return view('admin.heroes.edit', [
            'hero' => $hero,
            'bannerFunctionalities' => $this->bannerFunctionalities(),
        ]);
    }

    public function update(UpdateHomepageHeroRequest $request, HomepageHero $hero)
    {
        $data = [
            'title' => $request->title,
            'functionality_name' => $request->input('functionality_name'),
            'event_name' => $request->input('event_name'),
            'url' => $request->input('url'),
            'external_url' => $request->input('external_url'),
        ];

        if ($request->hasFile('mobile_image')) {
            // delete old image
            if ($hero->image_path && Storage::disk('public')->exists($hero->image_path)) {
                Storage::disk('public')->delete($hero->image_path);
            }
            $data['image_path'] = $request->file('mobile_image')->store('hero_images', 'public');
        }

        if ($request->hasFile('web_image')) {
            // delete old web image
            if ($hero->web_image_path && Storage::disk('public')->exists($hero->web_image_path)) {
                Storage::disk('public')->delete($hero->web_image_path);
            }
            $data['web_image_path'] = $request->file('web_image')->store('hero_images', 'public');
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

        if ($hero->web_image_path && Storage::disk('public')->exists($hero->web_image_path)) {
            Storage::disk('public')->delete($hero->web_image_path);
        }

        $hero->delete();

        return redirect()
            ->route('heroes.index')
            ->with('success', 'Hero deleted successfully.');
    }

    /**
     * System destinations shown as friendly functionality and event names.
     * Both fields remain editable so newly added application links work
     * without requiring another code change.
     */
    private function bannerFunctionalities(): array
    {
        return [
            // 'Dashboard' => [
            //     'base_url' => '/dashboard',
            //     'events' => [
            //         'Dashboard Home' => [
            //             ['label' => 'Dashboard Home', 'url' => '/dashboard'],
            //         ],
            //     ],
            // ],
            'Business' => [
                'base_url' => '/api/business',
                'events' => [
                    'Business Profile' => Business::query()->orderBy('business_name')->get(['id', 'business_name'])
                        ->map(fn (Business $business) => [
                            'label' => $business->business_name,
                            'url' => '/api/business/'.$business->id,
                        ])->values()->all(),
                    // 'Product' => Product::query()->orderBy('name')->get(['id', 'name'])
                    //     ->map(fn (Product $product) => [
                    //         'label' => $product->name,
                    //         'url' => '/api/business/products/'.$product->id,
                    //     ])->values()->all(),
                    // 'Service' => Service::query()->orderBy('name')->get(['id', 'name'])
                    //     ->map(fn (Service $service) => [
                    //         'label' => $service->name,
                    //         'url' => '/api/business/services/'.$service->id,
                    //     ])->values()->all(),
                ],
            ],
            // 'Matrimony' => [
            //     'base_url' => '/api/matrimony',
            //     'events' => [
            //         'Matrimony Profile' => MatrimonyProfile::query()->with('user:id,name')->orderBy('id')->get()
            //             ->map(fn (MatrimonyProfile $profile) => [
            //                 'label' => $profile->candidate_name ?: $profile->user?->name ?: 'Profile #'.$profile->id,
            //                 'url' => '/api/matrimony/profile/'.$profile->id,
            //             ])->values()->all(),
            //     ],
            // ],
            // 'Webinars' => [
            //     'base_url' => '/api/webinars',
            //     'events' => [
            //         'Webinar Detail' => Webinar::query()->orderBy('title')->get(['id', 'title'])
            //             ->map(fn (Webinar $webinar) => [
            //                 'label' => $webinar->title,
            //                 'url' => '/api/webinars/'.$webinar->id,
            //             ])->values()->all(),
            //     ],
            // ],
            // 'Blog Portal' => [
            //     'base_url' => '/api/blogs',
            //     'events' => [
            //         'Blog Detail' => Blog::query()->orderBy('title')->get(['id', 'title'])
            //             ->map(fn (Blog $blog) => [
            //                 'label' => $blog->title,
            //                 'url' => '/api/blogs/'.$blog->id,
            //             ])->values()->all(),
            //     ],
            // ],
            // 'Jobs' => [
            //     'base_url' => '/api/jobs',
            //     'events' => [
            //         'Job Detail' => JobPosting::query()->orderBy('title')->get(['id', 'title'])
            //             ->map(fn (JobPosting $job) => [
            //                 'label' => $job->title,
            //                 'url' => '/api/jobs/'.$job->id,
            //             ])->values()->all(),
            //     ],
            // ],
            // 'Volunteers' => [
            //     'base_url' => '/api/volunteer/opportunities',
            //     'events' => [
            //         'Volunteer Opportunity' => VolunteerOpportunity::query()->orderBy('title')->get(['id', 'title'])
            //             ->map(fn (VolunteerOpportunity $opportunity) => [
            //                 'label' => $opportunity->title,
            //                 'url' => '/api/volunteer/opportunity/'.$opportunity->id,
            //             ])->values()->all(),
            //     ],
            // ],
            // 'Donations' => [
            //     'base_url' => '/api/donation/causes',
            //     'events' => [
            //         'Donation Cause' => DonationCause::query()->orderBy('title')->get(['id', 'title'])
            //             ->map(fn (DonationCause $cause) => [
            //                 'label' => $cause->title,
            //                 'url' => '/api/donation/cause/'.$cause->id,
            //             ])->values()->all(),
            //     ],
            // ],
        ];
    }
}
