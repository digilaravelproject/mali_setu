<?php

/* Each action is separately assignable. Route patterns are enforced by the
   same permission list displayed on the Teams form. */
return [
    'groups' => [
        'Dashboard & Insights' => [
            'dashboard' => ['label' => 'Dashboard', 'icon' => 'tachometer-alt', 'sections' => [
                'Overview' => [
                    ['key' => 'dashboard.view', 'label' => 'Open dashboard', 'description' => 'See summary cards and recent activity.', 'level' => 'view', 'routes' => ['admin.dashboard']],
                ],
            ]],
            'analytics' => ['label' => 'Analytics', 'icon' => 'chart-line', 'sections' => [
                'Analytics' => [
                    ['key' => 'analytics.view', 'label' => 'View analytics', 'description' => 'See charts, trends and analytical data.', 'level' => 'view', 'routes' => ['admin.analytics']],
                ],
            ]],
            'reports' => ['label' => 'Reports', 'icon' => 'file-pdf', 'sections' => [
                'Reports' => [
                    ['key' => 'reports.view', 'label' => 'View reports', 'description' => 'Open the reports page.', 'level' => 'view', 'routes' => ['admin.reports']],
                    ['key' => 'reports.download', 'label' => 'Download reports', 'description' => 'Download PDF or spreadsheet reports.', 'level' => 'action', 'routes' => ['admin.reports.download*']],
                ],
            ]],
        ],
        'People & Content' => [
            'users' => ['label' => 'User Management', 'icon' => 'users', 'sections' => [
                'All Users' => [
                    ['key' => 'users.list', 'label' => 'List users', 'description' => 'Open and search the user list.', 'level' => 'view', 'routes' => ['admin.users.index', 'admin.users.pending']],
                    ['key' => 'users.view', 'label' => 'View user', 'description' => 'Open an individual user profile.', 'level' => 'view', 'routes' => ['admin.users.show']],
                    ['key' => 'users.create', 'label' => 'Add user', 'description' => 'Create a new user account.', 'level' => 'action', 'routes' => ['admin.users.create', 'admin.users.store']],
                    ['key' => 'users.edit', 'label' => 'Edit user', 'description' => 'Change user information.', 'level' => 'action', 'routes' => ['admin.users.edit', 'admin.users.update']],
                    ['key' => 'users.status', 'label' => 'Activate / suspend', 'description' => 'Change whether a user can use the platform.', 'level' => 'action', 'routes' => ['admin.users.activate', 'admin.users.suspend']],
                    ['key' => 'users.export', 'label' => 'Download PDF', 'description' => 'Download an individual user profile PDF.', 'level' => 'action', 'routes' => ['admin.users.pdf']],
                    ['key' => 'users.delete', 'label' => 'Delete user', 'description' => 'Permanently remove a user.', 'level' => 'action', 'routes' => ['admin.users.destroy']],
                ],
                'Verifications' => [
                    ['key' => 'users.verification.list', 'label' => 'View verifications', 'description' => 'See pending caste certificate verifications.', 'level' => 'view', 'routes' => ['admin.users.verification.pending']],
                    ['key' => 'users.verification.manage', 'label' => 'Approve / reject', 'description' => 'Approve, reject or verify a certificate.', 'level' => 'action', 'routes' => ['admin.users.approve-certificate', 'admin.users.reject-certificate', 'admin.users.verify']],
                ],
            ]],
            'bloggers' => ['label' => 'Manage Blogger', 'icon' => 'users-cog', 'sections' => [
                'Bloggers' => [
                    ['key' => 'bloggers.list', 'label' => 'List bloggers', 'description' => 'View blogger accounts.', 'level' => 'view', 'routes' => ['admin.bloggers.index', 'admin.bloggers.show']],
                    ['key' => 'bloggers.create', 'label' => 'Add blogger', 'description' => 'Create a blogger account.', 'level' => 'action', 'routes' => ['admin.bloggers.create', 'admin.bloggers.store', 'admin.bloggers.check-email']],
                    ['key' => 'bloggers.edit', 'label' => 'Edit blogger', 'description' => 'Update blogger details.', 'level' => 'action', 'routes' => ['admin.bloggers.edit', 'admin.bloggers.update']],
                    ['key' => 'bloggers.status', 'label' => 'Change status', 'description' => 'Activate or deactivate a blogger.', 'level' => 'action', 'routes' => ['admin.bloggers.toggle-status']],
                    ['key' => 'bloggers.delete', 'label' => 'Delete blogger', 'description' => 'Remove a blogger account.', 'level' => 'action', 'routes' => ['admin.bloggers.destroy']],
                ],
                'Blog Access' => [
                    ['key' => 'bloggers.access.view', 'label' => 'View blog access', 'description' => 'See which users have blog access.', 'level' => 'view', 'routes' => ['admin.blogs.access']],
                    ['key' => 'bloggers.access.manage', 'label' => 'Change blog access', 'description' => 'Grant or revoke blog access.', 'level' => 'action', 'routes' => ['admin.blogs.toggle-user-access']],
                ],
            ]],
            'homepage_heroes' => ['label' => 'Homepage Heroes', 'icon' => 'image', 'sections' => [
                'Homepage Heroes' => [
                    ['key' => 'homepage_heroes.list', 'label' => 'List banners', 'description' => 'View homepage hero banners.', 'level' => 'view', 'routes' => ['heroes.index', 'heroes.show']],
                    ['key' => 'homepage_heroes.create', 'label' => 'Add banner', 'description' => 'Create a homepage banner.', 'level' => 'action', 'routes' => ['heroes.create', 'heroes.store']],
                    ['key' => 'homepage_heroes.edit', 'label' => 'Edit banner', 'description' => 'Update a homepage banner.', 'level' => 'action', 'routes' => ['heroes.edit', 'heroes.update']],
                    ['key' => 'homepage_heroes.delete', 'label' => 'Delete banner', 'description' => 'Remove a homepage banner.', 'level' => 'action', 'routes' => ['heroes.destroy']],
                ],
            ]],
            'blogs' => ['label' => 'Manage Blogs', 'icon' => 'newspaper', 'sections' => [
                'Blogs' => [
                    ['key' => 'blogs.list', 'label' => 'List blogs', 'description' => 'View all blog posts.', 'level' => 'view', 'routes' => ['admin.blogs.index']],
                    ['key' => 'blogs.create', 'label' => 'Add blog', 'description' => 'Create and publish a post.', 'level' => 'action', 'routes' => ['admin.blogs.create', 'admin.blogs.store']],
                    ['key' => 'blogs.edit', 'label' => 'Edit blog', 'description' => 'Update a blog post.', 'level' => 'action', 'routes' => ['admin.blogs.edit', 'admin.blogs.update']],
                    ['key' => 'blogs.status', 'label' => 'Publish / unpublish', 'description' => 'Change blog visibility.', 'level' => 'action', 'routes' => ['admin.blogs.toggle-status']],
                    ['key' => 'blogs.delete', 'label' => 'Delete blog / comment', 'description' => 'Delete a blog or one of its comments.', 'level' => 'action', 'routes' => ['admin.blogs.destroy', 'admin.blogs.comments.destroy', 'admin.blog-comments.destroy']],
                ],
                'Blog Categories' => [
                    ['key' => 'blogs.categories.list', 'label' => 'List categories', 'description' => 'View blog categories and details.', 'level' => 'view', 'routes' => ['admin.blog-categories.index', 'admin.blog-categories.show']],
                    ['key' => 'blogs.categories.create', 'label' => 'Add category', 'description' => 'Create a blog category.', 'level' => 'action', 'routes' => ['admin.blog-categories.create', 'admin.blog-categories.store']],
                    ['key' => 'blogs.categories.edit', 'label' => 'Edit category', 'description' => 'Update a blog category.', 'level' => 'action', 'routes' => ['admin.blog-categories.edit', 'admin.blog-categories.update']],
                    ['key' => 'blogs.categories.status', 'label' => 'Change status', 'description' => 'Activate or deactivate a category.', 'level' => 'action', 'routes' => ['admin.blog-categories.toggle-status']],
                    ['key' => 'blogs.categories.delete', 'label' => 'Delete category', 'description' => 'Remove a blog category.', 'level' => 'action', 'routes' => ['admin.blog-categories.destroy']],
                ],
            ]],
            'education' => ['label' => 'Manage Education', 'icon' => 'graduation-cap', 'sections' => [
                'Education' => [
                    ['key' => 'education.list', 'label' => 'List education', 'description' => 'View education entries.', 'level' => 'view', 'routes' => ['admin.educations.index']],
                    ['key' => 'education.create', 'label' => 'Add education', 'description' => 'Create an education entry.', 'level' => 'action', 'routes' => ['admin.educations.create', 'admin.educations.store']],
                    ['key' => 'education.edit', 'label' => 'Edit education', 'description' => 'Update an education entry.', 'level' => 'action', 'routes' => ['admin.educations.edit', 'admin.educations.update']],
                    ['key' => 'education.status', 'label' => 'Change status', 'description' => 'Activate or deactivate an entry.', 'level' => 'action', 'routes' => ['admin.educations.toggle-status']],
                    ['key' => 'education.delete', 'label' => 'Delete education', 'description' => 'Remove an education entry.', 'level' => 'action', 'routes' => ['admin.educations.destroy']],
                ],
            ]],
        ],
        'Business & Careers' => [
            'categories' => ['label' => 'Category', 'icon' => 'shopping-bag', 'sections' => [
                'Business Categories' => [
                    ['key' => 'categories.list', 'label' => 'List categories', 'description' => 'View business categories.', 'level' => 'view', 'routes' => ['admin.category.index']],
                    ['key' => 'categories.create', 'label' => 'Add category', 'description' => 'Create a business category.', 'level' => 'action', 'routes' => ['admin.category.addcategory', 'admin.category.storecategory']],
                    ['key' => 'categories.edit', 'label' => 'Edit category', 'description' => 'Update a business category.', 'level' => 'action', 'routes' => ['admin.category.edit', 'admin.category.update']],
                    ['key' => 'categories.order', 'label' => 'Reorder / move', 'description' => 'Change ordering and page placement.', 'level' => 'action', 'routes' => ['admin.category.reorder', 'admin.category.moveToPage']],
                    ['key' => 'categories.status', 'label' => 'Activate / deactivate', 'description' => 'Change category availability.', 'level' => 'action', 'routes' => ['admin.category.active', 'admin.category.inactive']],
                    ['key' => 'categories.delete', 'label' => 'Delete category', 'description' => 'Remove a business category.', 'level' => 'action', 'routes' => ['admin.category.destroy']],
                ],
            ]],
            'businesses' => ['label' => 'Businesses', 'icon' => 'building', 'sections' => [
                'All Businesses' => [
                    ['key' => 'businesses.list', 'label' => 'List businesses', 'description' => 'View and search businesses.', 'level' => 'view', 'routes' => ['admin.businesses.index']],
                    ['key' => 'businesses.view', 'label' => 'View business', 'description' => 'Open business details.', 'level' => 'view', 'routes' => ['admin.businesses.show', 'admin.businesses.geocode-pincode']],
                    ['key' => 'businesses.create', 'label' => 'Add business', 'description' => 'Create a business.', 'level' => 'action', 'routes' => ['admin.businesses.create', 'admin.businesses.store']],
                    ['key' => 'businesses.edit', 'label' => 'Edit business', 'description' => 'Update business information.', 'level' => 'action', 'routes' => ['admin.businesses.edit', 'admin.businesses.update']],
                    ['key' => 'businesses.status', 'label' => 'Activate / suspend', 'description' => 'Change business availability.', 'level' => 'action', 'routes' => ['admin.businesses.activate', 'admin.businesses.suspend']],
                    ['key' => 'businesses.delete', 'label' => 'Delete business', 'description' => 'Permanently remove a business.', 'level' => 'action', 'routes' => ['admin.businesses.destroy']],
                ],
                'Business Verification' => [
                    ['key' => 'businesses.verification.list', 'label' => 'View verification', 'description' => 'See pending business verification.', 'level' => 'view', 'routes' => ['admin.businesses.pending', 'admin.businesses.verification']],
                    ['key' => 'businesses.verification.manage', 'label' => 'Approve / reject', 'description' => 'Approve or reject businesses.', 'level' => 'action', 'routes' => ['admin.businesses.approve', 'admin.businesses.reject']],
                ],
                'Business Jobs' => [
                    ['key' => 'businesses.jobs.list', 'label' => 'View business jobs', 'description' => 'See jobs for a business.', 'level' => 'view', 'routes' => ['admin.businesses.jobs']],
                    ['key' => 'businesses.jobs.moderate', 'label' => 'Approve / reject jobs', 'description' => 'Moderate jobs from business details.', 'level' => 'action', 'routes' => ['admin.businesses.jobs.approve', 'admin.businesses.jobs.reject']],
                ],
            ]],
            'jobs' => ['label' => 'Jobs & Careers', 'icon' => 'briefcase', 'sections' => [
                'All Jobs' => [
                    ['key' => 'jobs.list', 'label' => 'List jobs', 'description' => 'View and filter all jobs.', 'level' => 'view', 'routes' => ['admin.jobs.index']],
                    ['key' => 'jobs.create', 'label' => 'Add job', 'description' => 'Create a job for a business.', 'level' => 'action', 'routes' => ['admin.jobs.create', 'admin.jobs.store']],
                    ['key' => 'jobs.edit', 'label' => 'Edit job', 'description' => 'Update job information.', 'level' => 'action', 'routes' => ['admin.jobs.edit', 'admin.jobs.update']],
                    ['key' => 'jobs.status', 'label' => 'Show / hide job', 'description' => 'Change job visibility.', 'level' => 'action', 'routes' => ['admin.jobs.toggle']],
                    ['key' => 'jobs.delete', 'label' => 'Delete job', 'description' => 'Delete a job and its applications.', 'level' => 'action', 'routes' => ['admin.jobs.destroy']],
                ],
                'Applications' => [
                    ['key' => 'jobs.applications.list', 'label' => 'List applications', 'description' => 'View job applications.', 'level' => 'view', 'routes' => ['admin.jobs.applications']],
                    ['key' => 'jobs.applications.status', 'label' => 'Change application status', 'description' => 'Review, accept or reject an application.', 'level' => 'action', 'routes' => ['admin.jobs.applications.status']],
                    ['key' => 'jobs.applications.delete', 'label' => 'Delete application', 'description' => 'Remove a job application.', 'level' => 'action', 'routes' => ['admin.jobs.applications.destroy']],
                ],
            ]],
            'plans' => ['label' => 'Manage Plans', 'icon' => 'list-alt', 'sections' => [
                'Business Plans' => [
                    ['key' => 'plans.business.list', 'label' => 'List business plans', 'description' => 'View business plans.', 'level' => 'view', 'routes' => ['admin.plans.business.index']],
                    ['key' => 'plans.business.create', 'label' => 'Add business plan', 'description' => 'Create a business plan.', 'level' => 'action', 'routes' => ['admin.plans.business.create', 'admin.plans.business.store']],
                    ['key' => 'plans.business.edit', 'label' => 'Edit business plan', 'description' => 'Update a business plan.', 'level' => 'action', 'routes' => ['admin.plans.business.edit', 'admin.plans.business.update']],
                    ['key' => 'plans.business.delete', 'label' => 'Delete business plan', 'description' => 'Remove a business plan.', 'level' => 'action', 'routes' => ['admin.plans.business.destroy']],
                ],
                'Matrimony Plans' => [
                    ['key' => 'plans.matrimony.list', 'label' => 'List matrimony plans', 'description' => 'View matrimony plans.', 'level' => 'view', 'routes' => ['admin.plans.matrimony.index']],
                    ['key' => 'plans.matrimony.create', 'label' => 'Add matrimony plan', 'description' => 'Create a matrimony plan.', 'level' => 'action', 'routes' => ['admin.plans.matrimony.create', 'admin.plans.matrimony.store']],
                    ['key' => 'plans.matrimony.edit', 'label' => 'Edit matrimony plan', 'description' => 'Update a matrimony plan.', 'level' => 'action', 'routes' => ['admin.plans.matrimony.edit', 'admin.plans.matrimony.update']],
                    ['key' => 'plans.matrimony.delete', 'label' => 'Delete matrimony plan', 'description' => 'Remove a matrimony plan.', 'level' => 'action', 'routes' => ['admin.plans.matrimony.destroy']],
                ],
            ]],
        ],
        'Community Modules' => [
            'donations' => ['label' => 'Contribution', 'icon' => 'heart', 'sections' => [
                'Contributions' => [
                    ['key' => 'donations.list', 'label' => 'List contributions', 'description' => 'View contribution records.', 'level' => 'view', 'routes' => ['admin.donations.index', 'admin.donations.show']],
                    ['key' => 'donations.delete', 'label' => 'Delete contribution', 'description' => 'Remove a contribution record.', 'level' => 'action', 'routes' => ['admin.donations.destroy']],
                ],
                'Contribution Causes' => [
                    ['key' => 'donations.causes.list', 'label' => 'List causes', 'description' => 'View contribution causes.', 'level' => 'view', 'routes' => ['admin.donations.causes']],
                    ['key' => 'donations.causes.create', 'label' => 'Add cause', 'description' => 'Create a contribution cause.', 'level' => 'action', 'routes' => ['admin.donations.causes.create', 'admin.donations.causes.store']],
                    ['key' => 'donations.causes.edit', 'label' => 'Edit cause', 'description' => 'Update a contribution cause.', 'level' => 'action', 'routes' => ['admin.donations.causes.edit', 'admin.donations.causes.update']],
                    ['key' => 'donations.causes.status', 'label' => 'Change cause status', 'description' => 'Activate or deactivate a cause.', 'level' => 'action', 'routes' => ['admin.donations.causes.toggle-status']],
                    ['key' => 'donations.causes.delete', 'label' => 'Delete cause', 'description' => 'Remove a contribution cause.', 'level' => 'action', 'routes' => ['admin.donations.causes.destroy']],
                ],
            ]],
            'matrimony' => ['label' => 'Matrimony', 'icon' => 'heart', 'sections' => [
                'Matrimony Profiles' => [
                    ['key' => 'matrimony.profiles.list', 'label' => 'List profiles', 'description' => 'View and search matrimony profiles.', 'level' => 'view', 'routes' => ['admin.matrimony.index', 'admin.matrimony.show']],
                    ['key' => 'matrimony.profiles.create', 'label' => 'Add profile', 'description' => 'Create a matrimony profile.', 'level' => 'action', 'routes' => ['admin.matrimony.create', 'admin.matrimony.store']],
                    ['key' => 'matrimony.profiles.edit', 'label' => 'Edit profile', 'description' => 'Update a matrimony profile.', 'level' => 'action', 'routes' => ['admin.matrimony.edit', 'admin.matrimony.update']],
                    ['key' => 'matrimony.profiles.moderate', 'label' => 'Approve / reject', 'description' => 'Moderate matrimony profiles.', 'level' => 'action', 'routes' => ['admin.matrimony.moderation', 'admin.matrimony.approve', 'admin.matrimony.reject']],
                    ['key' => 'matrimony.profiles.status', 'label' => 'Activate / suspend', 'description' => 'Change profile account availability.', 'level' => 'action', 'routes' => ['admin.matrimony.activate', 'admin.matrimony.suspend']],
                    ['key' => 'matrimony.profiles.delete', 'label' => 'Delete profile', 'description' => 'Remove a matrimony profile.', 'level' => 'action', 'routes' => ['admin.matrimony.destroy']],
                ],
                'Request Analysis' => [
                    ['key' => 'matrimony.connections.view', 'label' => 'View request analysis', 'description' => 'See connections and request analysis.', 'level' => 'view', 'routes' => ['admin.matrimony.connections']],
                    ['key' => 'matrimony.connections.export', 'label' => 'Export connections', 'description' => 'Download connection analysis.', 'level' => 'action', 'routes' => ['admin.matrimony.connections.export']],
                ],
                'Chats' => [
                    ['key' => 'matrimony.chats.view', 'label' => 'View chats', 'description' => 'View matrimony chat conversations for moderation.', 'level' => 'view', 'routes' => ['admin.matrimony.chats']],
                ],
            ]],
            'casts' => ['label' => 'Manage Casts', 'icon' => 'sitemap', 'sections' => [
                'Casts' => [
                    ['key' => 'casts.list', 'label' => 'List casts', 'description' => 'View caste records.', 'level' => 'view', 'routes' => ['admin.casts.index']],
                    ['key' => 'casts.create', 'label' => 'Add cast', 'description' => 'Create a caste record.', 'level' => 'action', 'routes' => ['admin.casts.create', 'admin.casts.store']],
                    ['key' => 'casts.edit', 'label' => 'Edit cast', 'description' => 'Update a caste record.', 'level' => 'action', 'routes' => ['admin.casts.edit', 'admin.casts.update']],
                    ['key' => 'casts.status', 'label' => 'Change cast status', 'description' => 'Activate or deactivate a cast.', 'level' => 'action', 'routes' => ['admin.casts.toggle-status']],
                    ['key' => 'casts.delete', 'label' => 'Delete cast', 'description' => 'Remove a caste record.', 'level' => 'action', 'routes' => ['admin.casts.destroy']],
                ],
                'Sub-casts' => [
                    ['key' => 'casts.subcasts.list', 'label' => 'List sub-casts', 'description' => 'View sub-casts for a cast.', 'level' => 'view', 'routes' => ['admin.casts.subcasts.index']],
                    ['key' => 'casts.subcasts.create', 'label' => 'Add sub-cast', 'description' => 'Create a sub-cast.', 'level' => 'action', 'routes' => ['admin.casts.subcasts.create', 'admin.casts.subcasts.store']],
                    ['key' => 'casts.subcasts.edit', 'label' => 'Edit sub-cast', 'description' => 'Update a sub-cast.', 'level' => 'action', 'routes' => ['admin.casts.subcasts.edit', 'admin.casts.subcasts.update']],
                    ['key' => 'casts.subcasts.status', 'label' => 'Change sub-cast status', 'description' => 'Activate or deactivate a sub-cast.', 'level' => 'action', 'routes' => ['admin.casts.subcasts.toggle-status']],
                    ['key' => 'casts.subcasts.delete', 'label' => 'Delete sub-cast', 'description' => 'Remove a sub-cast.', 'level' => 'action', 'routes' => ['admin.casts.subcasts.destroy']],
                ],
            ]],
            'volunteers' => ['label' => 'Volunteers', 'icon' => 'hands-helping', 'sections' => [
                'Volunteers' => [
                    ['key' => 'volunteers.list', 'label' => 'List volunteers', 'description' => 'View volunteer profiles.', 'level' => 'view', 'routes' => ['admin.volunteers.index', 'admin.volunteers.show']],
                    ['key' => 'volunteers.verification', 'label' => 'View verification', 'description' => 'See pending volunteer verification.', 'level' => 'view', 'routes' => ['admin.volunteers.pending', 'admin.volunteers.verification']],
                    ['key' => 'volunteers.moderate', 'label' => 'Approve / reject', 'description' => 'Moderate volunteer profiles.', 'level' => 'action', 'routes' => ['admin.volunteers.approve', 'admin.volunteers.reject']],
                    ['key' => 'volunteers.delete', 'label' => 'Delete volunteer', 'description' => 'Remove a volunteer profile.', 'level' => 'action', 'routes' => ['admin.volunteers.destroy']],
                ],
            ]],
        ],
        'Finance & System' => [
            'payments' => ['label' => 'Payments', 'icon' => 'credit-card', 'sections' => [
                'Transactions' => [
                    ['key' => 'payments.list', 'label' => 'List payments', 'description' => 'View payment and transaction records.', 'level' => 'view', 'routes' => ['admin.payments.index', 'admin.payments.show', 'admin.payments.business', 'admin.payments.matrimony', 'admin.payments.transaction.show']],
                    ['key' => 'payments.status', 'label' => 'Update payment status', 'description' => 'Change a payment status.', 'level' => 'action', 'routes' => ['admin.payments.update-status']],
                    ['key' => 'payments.refund', 'label' => 'Issue refund', 'description' => 'Refund an eligible payment.', 'level' => 'action', 'routes' => ['admin.payments.refund']],
                    ['key' => 'payments.export', 'label' => 'Export payments', 'description' => 'Download payment PDF or spreadsheet files.', 'level' => 'action', 'routes' => ['admin.payments.export*', 'admin.payments.business.export*', 'admin.payments.matrimony.export*']],
                ],
                'Payment Analytics' => [
                    ['key' => 'payments.analytics', 'label' => 'View analytics', 'description' => 'See payment analytics and totals.', 'level' => 'view', 'routes' => ['admin.payments.analytics']],
                ],
            ]],
            'settings' => ['label' => 'Settings', 'icon' => 'cog', 'sections' => [
                'Other Settings' => [
                    ['key' => 'settings.general.view', 'label' => 'View settings', 'description' => 'Open general settings.', 'level' => 'view', 'routes' => ['admin.settings']],
                    ['key' => 'settings.general.update', 'label' => 'Update settings', 'description' => 'Change general settings.', 'level' => 'action', 'routes' => ['admin.settings.update']],
                ],
                'Support Number' => [
                    ['key' => 'settings.support.view', 'label' => 'View support numbers', 'description' => 'See configured support numbers.', 'level' => 'view', 'routes' => ['admin.support-numbers.index', 'admin.support-numbers.show']],
                    ['key' => 'settings.support.update', 'label' => 'Update support numbers', 'description' => 'Change support contact numbers.', 'level' => 'action', 'routes' => ['admin.support-numbers.update']],
                ],
                'Push Notifications' => [
                    ['key' => 'settings.notifications.list', 'label' => 'List notifications', 'description' => 'View notification actions.', 'level' => 'view', 'routes' => ['admin.notifications.index']],
                    ['key' => 'settings.notifications.create', 'label' => 'Add notification', 'description' => 'Create a notification action.', 'level' => 'action', 'routes' => ['admin.notifications.create', 'admin.notifications.store']],
                    ['key' => 'settings.notifications.edit', 'label' => 'Edit notification', 'description' => 'Update actions and payloads.', 'level' => 'action', 'routes' => ['admin.notifications.edit', 'admin.notifications.update', 'admin.notifications.payload.*']],
                    ['key' => 'settings.notifications.send', 'label' => 'Send notification', 'description' => 'Send push or Firebase notifications.', 'level' => 'action', 'routes' => ['admin.notifications.send-form', 'admin.notifications.send', 'admin.notifications.firebase.update']],
                    ['key' => 'settings.notifications.settings', 'label' => 'Notification settings', 'description' => 'Change global notification settings.', 'level' => 'action', 'routes' => ['admin.notifications.settings.update']],
                    ['key' => 'settings.notifications.delete', 'label' => 'Delete notification', 'description' => 'Remove a notification action.', 'level' => 'action', 'routes' => ['admin.notifications.destroy']],
                ],
                'App Version' => [
                    ['key' => 'settings.app_version.view', 'label' => 'View app versions', 'description' => 'See mobile app version requirements.', 'level' => 'view', 'routes' => ['admin.app-versions.index']],
                    ['key' => 'settings.app_version.update', 'label' => 'Update app versions', 'description' => 'Change supported and required versions.', 'level' => 'action', 'routes' => ['admin.app-versions.update']],
                ],
                'System Actions' => [
                    ['key' => 'settings.system.run', 'label' => 'Run system actions', 'description' => 'Clear caches/logs, optimize, migrate, back up or generate keys.', 'level' => 'action', 'routes' => ['admin.system.*']],
                ],
            ]],
        ],
    ],
];
