# Mali Setu API additions

Set these shell variables before using the examples:

```bash
BASE_URL="https://malisetu.com"
ACCESS_TOKEN="YOUR_SANCTUM_ACCESS_TOKEN"
```

## Add a product with multiple images

```bash
curl --request POST "$BASE_URL/api/business/products" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $ACCESS_TOKEN" \
  --form "business_id=12" \
  --form "name=Organic Fertilizer" \
  --form "description=Premium organic fertilizer" \
  --form "cost=499.00" \
  --form "images[]=@/absolute/path/front.jpg" \
  --form "images[]=@/absolute/path/back.jpg"
```

## Add a service with multiple images

```bash
curl --request POST "$BASE_URL/api/business/services" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $ACCESS_TOKEN" \
  --form "business_id=12" \
  --form "name=Garden Maintenance" \
  --form "description=Monthly garden maintenance service" \
  --form "cost=1500.00" \
  --form "images[]=@/absolute/path/before.jpg" \
  --form "images[]=@/absolute/path/after.jpg"
```

## Update a product

`POST` plus `_method=PUT` is used so PHP receives multipart files reliably. Existing images are kept unless their stored paths are sent in `remove_image_paths[]`.

```bash
curl --request POST "$BASE_URL/api/business/products/125" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $ACCESS_TOKEN" \
  --form "_method=PUT" \
  --form "name=Organic Fertilizer - Updated" \
  --form "description=Updated product details" \
  --form "cost=549.00" \
  --form "status=active" \
  --form "images[]=@/absolute/path/new-photo.jpg" \
  --form "remove_image_paths[]=products/old-photo.jpg"
```

## Update a service

```bash
curl --request POST "$BASE_URL/api/business/services/88" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $ACCESS_TOKEN" \
  --form "_method=PUT" \
  --form "name=Premium Garden Maintenance" \
  --form "description=Updated service details" \
  --form "cost=1800.00" \
  --form "status=active" \
  --form "images[]=@/absolute/path/service-new.jpg"
```

## Delete a product

```bash
curl --request DELETE "$BASE_URL/api/business/products/125" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $ACCESS_TOKEN"
```

## Delete a service

```bash
curl --request DELETE "$BASE_URL/api/business/services/88" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $ACCESS_TOKEN"
```

## Delete a business

```bash
curl --request DELETE "$BASE_URL/api/business/12" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $ACCESS_TOKEN"
```

## Send phone OTP with Firebase (no access token)

Firebase requires an app-verification token to protect phone-auth SMS requests. Send the reCAPTCHA token produced by the client app; this endpoint itself does not require a Sanctum access token.

```bash
curl --request POST "$BASE_URL/api/auth/phone/send-otp" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data '{
    "phone_number": "+919876543210",
    "recaptcha_token": "FIREBASE_RECAPTCHA_TOKEN"
  }'
```

## Resend phone OTP with Firebase (no access token)

```bash
curl --request POST "$BASE_URL/api/auth/phone/resend-otp" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data '{
    "phone_number": "+919876543210",
    "recaptcha_token": "NEW_FIREBASE_RECAPTCHA_TOKEN"
  }'
```

## Get support numbers (no access token)

```bash
curl --request GET "$BASE_URL/api/support-numbers" \
  --header "Accept: application/json"
```

## Push payload delivered to Flutter

Every FCM message is normalized to this inner payload (FCM HTTP v1 wraps it in a top-level `message` object):

```json
{
  "notification": {
    "title": "New Lead Assigned",
    "body": "A new lead has been assigned to you."
  },
  "data": {
    "type": "lead",
    "action": "details",
    "id": "125",
    "click_action": "FLUTTER_NOTIFICATION_CLICK"
  }
}
```
