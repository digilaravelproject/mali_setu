# Webinar and registration API cURL collection

Create these Postman collection/environment variables first:

```text
base_url = http://localhost/mali_setu/public
token = PASTE_SANCTUM_TOKEN_HERE
webinar_id = 1
```

## Register a business or matrimony user (multipart certificate upload)

`cast_certificate` is required when `user_type` is `business` or `matrimony`. It accepts JPG, PNG or PDF up to 5 MB. New users are saved with `caste_verification_status=pending`.

```bash
curl --request POST "{{base_url}}/api/auth/register" \
  --header "Accept: application/json" \
  --form "name=Test Matrimony User" \
  --form "email=matrimony.user@example.com" \
  --form "phone=9876543210" \
  --form "password=Password@123" \
  --form "password_confirmation=Password@123" \
  --form "user_type=matrimony" \
  --form "term_condition=1" \
  --form "cast_certificate=@C:/path/to/caste-certificate.pdf"
```

The same endpoint also accepts a base64 data URI in JSON:

```bash
curl --request POST "{{base_url}}/api/auth/register" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data-raw '{
    "name": "Test Business User",
    "email": "business.user@example.com",
    "phone": "9876543211",
    "password": "Password@123",
    "password_confirmation": "Password@123",
    "user_type": "business",
    "term_condition": true,
    "cast_certificate": "data:application/pdf;base64,PASTE_BASE64_HERE"
  }'
```

## Login and obtain a token (after admin approval)

```bash
curl --request POST "{{base_url}}/api/auth/login" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data-raw '{
    "email": "approved.user@example.com",
    "password": "Password@123"
  }'
```

Copy `data.token` from the response into the Postman `token` variable. The register response also returns a token immediately.

## Banner with the next three webinars

```bash
curl --request GET "{{base_url}}/api/banner/" \
  --header "Accept: application/json"
```

The response contains the existing `data` banner array and an `upcoming_webinars` array (maximum three records).

## All webinars

```bash
curl --request GET "{{base_url}}/api/webinars?per_page=12&page=1" \
  --header "Accept: application/json"
```

## Search webinars

```bash
curl --request GET "{{base_url}}/api/webinars?search=business&per_page=12&page=1" \
  --header "Accept: application/json"
```

Optional status filters are `upcoming` and `completed`:

```bash
curl --request GET "{{base_url}}/api/webinars?status=upcoming&search=growth" \
  --header "Accept: application/json"
```

## Upcoming three webinars directly

```bash
curl --request GET "{{base_url}}/api/webinars/upcoming" \
  --header "Accept: application/json"
```

## Get webinar by ID

```bash
curl --request GET "{{base_url}}/api/webinars/{{webinar_id}}" \
  --header "Accept: application/json"
```

## Register for a webinar

```bash
curl --request POST "{{base_url}}/api/webinars/{{webinar_id}}/register" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

Registration is idempotent. The registered user receives webinar details and the stored Agora join link by email.

## Get webinar comments with owner details

```bash
curl --request GET "{{base_url}}/api/webinars/{{webinar_id}}/comments?page=1" \
  --header "Accept: application/json"
```

Each comment includes its owner’s `id`, `name`, and `photo`.

## Post a webinar comment

```bash
curl --request POST "{{base_url}}/api/webinars/{{webinar_id}}/comments" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}" \
  --header "Content-Type: application/json" \
  --data-raw '{"comment":"Can you share the presentation after the webinar?"}'
```

## My webinar registrations

```bash
curl --request GET "{{base_url}}/api/my-webinar-registrations?page=1" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

## Agora configuration

Add the Agora credentials to `.env`, then clear Laravel's configuration cache:

```dotenv
AGORA_APP_ID=your_agora_app_id
AGORA_APP_CERTIFICATE=your_agora_app_certificate
AGORA_TOKEN_EXPIRATION=3600
```

```bash
php artisan config:clear
```

The Laravel server generates a short-lived AccessToken2 for the authenticated user and webinar channel. Never expose `AGORA_APP_CERTIFICATE` in browser or mobile code.

## Join token for the live webinar

Available to an authorized, registered attendee from the scheduled start time until the webinar ends:

```bash
curl --request GET "{{base_url}}/api/webinars/{{webinar_id}}/join-token" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

Use the returned `app_id`, `channel`, `uid`, and `token` with the Agora SDK in `live` mode. Joined members use the `host` SDK role so they can publish their own microphone, camera, and screen; application moderation remains restricted to the administrator. The web client renews this token automatically before expiry.
