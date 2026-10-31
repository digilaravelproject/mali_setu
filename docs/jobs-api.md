# Jobs API

All responses use the application's existing job and application fields. No job-related database migration is required.

## Get and filter jobs

`GET /api/jobs`

Optional query parameters:

- `search`: job title, description, requirements, or business name
- `business_id`
- `category`
- `location`
- `experience_level`: `entry`, `junior`, `mid`, `senior`, `executive`
- `employment_type`: `full-time`, `part-time`, `contract`, `freelance`, `internship`
- `job_type`
- `per_page`: 1–100 (default 15)

Only approved, active, non-expired jobs are returned. Each item includes `applications_count` and `is_liked` (when a valid bearer token is supplied).

Example: `GET /api/jobs?category=IT%20%26%20Software&location=Pune&employment_type=full-time&search=Laravel`

## My applications

`GET /api/jobs/my-applications`

Requires a Sanctum bearer token. `POST` remains supported for backward compatibility.

Optional query parameters:

- `status`: `pending`, `reviewed`, `accepted`, or `rejected`
- `per_page`: 1–100 (default 15)

## Like / unlike a job

`POST /api/jobs/{job_id}/like-toggle`

Requires a Sanctum bearer token. Calling the endpoint toggles the current state and returns:

```json
{
  "success": true,
  "message": "Job saved successfully.",
  "data": {
    "job_id": 12,
    "is_liked": true
  }
}
```

Liked job IDs use Laravel's configured cache store, so no database field or migration is added.
