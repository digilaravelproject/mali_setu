# Business product/service enquiry APIs

Replace `https://malisetu.com` and `YOUR_SANCTUM_TOKEN` as needed. All enquiry APIs require a Sanctum bearer token and use JSON.

## 1. Enquire about another user's product

```bash
curl --location 'https://malisetu.com/api/business/enquiries' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer YOUR_SANCTUM_TOKEN' \
  --data '{
    "product_id": 12,
    "price": 1499.00,
    "description": "I need 3 units. Please confirm final price, availability and delivery time to Pune."
  }'
```

## 2. Enquire about another user's service

```bash
curl --location 'https://malisetu.com/api/business/enquiries' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --header 'Authorization: Bearer YOUR_SANCTUM_TOKEN' \
  --data '{
    "service_id": 8,
    "price": 5000.00,
    "description": "Please share the scope, earliest available date and whether travel charges are included."
  }'
```

Send exactly one of `product_id` or `service_id`. `price` is optional; when omitted, the current catalog price is copied into the enquiry. A user cannot enquire about their own business.

## 3. List enquiries sent or received by the logged-in user

```bash
curl --location --get 'https://malisetu.com/api/business/enquiries' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer YOUR_SANCTUM_TOKEN' \
  --data-urlencode 'per_page=20' \
  --data-urlencode 'page=1'
```

The response includes enquiries created by the logged-in user and enquiries received by businesses owned by that user.

## 4. Get complete enquiry details

```bash
curl --location 'https://malisetu.com/api/business/enquiries/25' \
  --header 'Accept: application/json' \
  --header 'Authorization: Bearer YOUR_SANCTUM_TOKEN'
```

The response contains the enquiring user's name/email/phone/photo, the product or service, and the business, business owner, and category. Only the sender or receiving business owner can view it.

## 5. Obtain a test token (existing login API)

```bash
curl --location 'https://malisetu.com/api/auth/login' \
  --header 'Accept: application/json' \
  --header 'Content-Type: application/json' \
  --data '{
    "email": "user@example.com",
    "password": "YOUR_PASSWORD"
  }'
```
