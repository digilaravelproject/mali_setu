# New Features API cURL Collection

Set these Postman environment variables first:

- `base_url`: `http://127.0.0.1/mali_setu/public/api` (change if your virtual host is different)
- `token`: the `token` returned by Login/Register
- `business_id`, `product_id`, `service_id`, `webinar_id`: IDs returned by the corresponding APIs

All authenticated requests use `Authorization: Bearer {{token}}`.

## 1. Login and get token

```bash
curl --request POST "{{base_url}}/auth/login" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data-raw '{
    "email": "user@example.com",
    "password": "password"
  }'
```

## 2. Convert current user to volunteer

This endpoint is idempotent. It changes `user_type` to `volunteer`, creates a pending volunteer profile, and returns the updated user.

```bash
curl --request POST "{{base_url}}/volunteer/convert" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

## 3. Add a product with multiple photos

Use one `images[]` form part per photo. Maximum: 10 photos, 5 MB each through the API.

```bash
curl --request POST "{{base_url}}/business/products" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}" \
  --form "business_id={{business_id}}" \
  --form "name=Premium Organic Seeds" \
  --form "description=High quality certified organic seeds" \
  --form "cost=499.00" \
  --form "images[]=@C:/Users/YourName/Pictures/product-1.jpg" \
  --form "images[]=@C:/Users/YourName/Pictures/product-2.jpg"
```

## 4. Update a product and add/remove photos

`remove_image_paths[]` must contain an existing path returned in the product's `images[].path` field.

```bash
curl --request POST "{{base_url}}/business/products/{{product_id}}" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}" \
  --form "name=Premium Organic Seeds - Updated" \
  --form "description=Updated product details" \
  --form "cost=549.00" \
  --form "images[]=@C:/Users/YourName/Pictures/product-3.jpg" \
  --form "remove_image_paths[]=products/old-image.jpg"
```

## 5. Get one product (includes all photo URLs)

```bash
curl --request GET "{{base_url}}/business/products/{{product_id}}" \
  --header "Accept: application/json"
```

## 6. Add a service with multiple photos

```bash
curl --request POST "{{base_url}}/business/services" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}" \
  --form "business_id={{business_id}}" \
  --form "name=Drip Irrigation Installation" \
  --form "description=Complete installation and setup service" \
  --form "cost=2500.00" \
  --form "images[]=@C:/Users/YourName/Pictures/service-1.jpg" \
  --form "images[]=@C:/Users/YourName/Pictures/service-2.jpg"
```

## 7. Update a service and add/remove photos

```bash
curl --request POST "{{base_url}}/business/services/{{service_id}}" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}" \
  --form "name=Drip Irrigation Installation - Updated" \
  --form "description=Updated service details" \
  --form "cost=2750.00" \
  --form "images[]=@C:/Users/YourName/Pictures/service-3.jpg" \
  --form "remove_image_paths[]=services/old-image.jpg"
```

## 8. Get one service (includes all photo URLs)

```bash
curl --request GET "{{base_url}}/business/services/{{service_id}}" \
  --header "Accept: application/json"
```

## 9. List webinars

Each webinar now includes `is_paid` and `price`.

```bash
curl --request GET "{{base_url}}/webinars?status=upcoming&per_page=12" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

## 10. Get webinar details

```bash
curl --request GET "{{base_url}}/webinars/{{webinar_id}}" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

## 11. Register for a free webinar

For a paid webinar this returns HTTP `402`; use the payment-order endpoint instead.

```bash
curl --request POST "{{base_url}}/webinars/{{webinar_id}}/register" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

## 12. Create a paid webinar payment order

The API always uses the administrator-defined price. Do not send an amount from the client.

```bash
curl --request POST "{{base_url}}/webinars/{{webinar_id}}/payment-order" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

The response contains `payment_url`, `encRequest`, and `access_code`. Submit the last two fields to CCAvenue from an HTML form:

```html
<form method="POST" action="PASTE_PAYMENT_URL_HERE">
  <input type="hidden" name="encRequest" value="PASTE_ENC_REQUEST_HERE">
  <input type="hidden" name="access_code" value="PASTE_ACCESS_CODE_HERE">
  <button type="submit">Pay now</button>
</form>
```

CCAvenue calls the encrypted callback automatically. A successful, amount-verified callback creates the webinar registration. Do not call the callback manually with fabricated data.

## 13. Check webinar payment/registration status

Poll this after the customer returns from CCAvenue. `payment_status` is `not_started`, `pending`, `completed`, `failed`, or `refunded`; `is_registered` becomes `true` only after successful payment.

```bash
curl --request GET "{{base_url}}/webinars/{{webinar_id}}/payment-status" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```

## 14. Get the user's webinar registrations

```bash
curl --request GET "{{base_url}}/my-webinar-registrations" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer {{token}}"
```
