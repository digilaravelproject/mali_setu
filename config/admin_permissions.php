<?php

return [
    'groups' => [
        'Dashboard & Insights' => [
            'dashboard' => ['label' => 'Dashboard', 'description' => 'View the main totals, recent activity and summary cards.'],
            'analytics' => ['label' => 'Analytics', 'description' => 'View charts, trends and analytical reports.'],
            'reports' => ['label' => 'Reports', 'description' => 'View reports; Manage also permits downloading report files.'],
        ],
        'People & Content' => [
            'users' => ['label' => 'User Management', 'description' => 'View members; Manage permits creating, editing, verifying, suspending and deleting users.'],
            'bloggers' => ['label' => 'Blogger Management', 'description' => 'View bloggers and access rules; Manage permits changing blogger accounts and access.'],
            'blogs' => ['label' => 'Blogs & Categories', 'description' => 'View blogs, categories and comments; Manage permits publishing, editing and deleting them.'],
            'homepage_heroes' => ['label' => 'Homepage Heroes', 'description' => 'View homepage banners; Manage permits creating, editing and deleting banners.'],
            'education' => ['label' => 'Education', 'description' => 'View education entries; Manage permits creating, editing and deleting them.'],
        ],
        'Business & Careers' => [
            'businesses' => ['label' => 'Businesses', 'description' => 'View businesses; Manage permits approval, editing, suspension and deletion.'],
            'categories' => ['label' => 'Business Categories', 'description' => 'View categories; Manage permits adding, sorting, activating and deleting categories.'],
            'jobs' => ['label' => 'Jobs & Careers', 'description' => 'View jobs and applications; Manage permits adding, editing, moderating and deleting them.'],
            'plans' => ['label' => 'Subscription Plans', 'description' => 'View business and matrimony plans; Manage permits changing plan pricing and availability.'],
        ],
        'Community Modules' => [
            'matrimony' => ['label' => 'Matrimony', 'description' => 'View profiles and request analysis; Manage permits profile moderation and account changes.'],
            'casts' => ['label' => 'Casts & Sub-casts', 'description' => 'View caste data; Manage permits creating, editing, sorting and deleting it.'],
            'donations' => ['label' => 'Contributions', 'description' => 'View contributions and causes; Manage permits changing causes and deleting records.'],
            'volunteers' => ['label' => 'Volunteers', 'description' => 'View volunteer records; Manage permits approval, rejection and deletion.'],
        ],
        'Finance & System' => [
            'payments' => ['label' => 'Payments', 'description' => 'View transactions; Manage permits refunds, status changes and financial exports.'],
            'settings' => ['label' => 'Settings', 'description' => 'View settings, support numbers, notifications and app versions; Manage permits changing them and running system actions.'],
        ],
    ],
];
