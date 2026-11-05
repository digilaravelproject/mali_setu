# Volunteer referral API cURL collection

Set these variables before running the protected calls:

```bash
BASE_URL="https://malisetu.com"
TOKEN="PASTE_LOGIN_TOKEN"
REFERRAL_CODE="MSU48291"
```

For local XAMPP, use `BASE_URL="http://localhost/mali_setu/public"` if the virtual host is not configured.

## 1. Register with an optional referral code

`referral_code` may be omitted or sent as an empty value. A primary volunteer is created inactive and receives no usable token until an administrator activates the account.

```bash
curl --request POST "$BASE_URL/api/auth/register" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data '{
    "name": "Darshan Kondekar",
    "email": "darshan@example.com",
    "phone": "9876543210",
    "password": "Password@123",
    "password_confirmation": "Password@123",
    "user_type": "general",
    "referral_code": "MSU48291",
    "term_condition": true
  }'
```

## 2. Login

```bash
curl --request POST "$BASE_URL/api/auth/login" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data '{
    "email": "darshan@example.com",
    "password": "Password@123"
  }'
```

## 3. Get volunteer details from a referral code

```bash
curl --request GET "$BASE_URL/api/referrals/$REFERRAL_CODE/volunteer" \
  --header "Accept: application/json"
```

## 4. Get a shareable referral link and message

```bash
curl --request GET "$BASE_URL/api/referrals/$REFERRAL_CODE/share-link" \
  --header "Accept: application/json"
```

## 5. Get referred users and their commission points

Optional query parameters are `user_type`, `status`, `per_page`, and `page`.

```bash
curl --request GET "$BASE_URL/api/referrals/$REFERRAL_CODE/referred-users?user_type=general&status=active&per_page=20&page=1" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $TOKEN"
```

## 6. Get referral history

`from` and `to` use `YYYY-MM-DD`.

```bash
curl --request GET "$BASE_URL/api/referrals/$REFERRAL_CODE/history?from=2026-01-01&to=2026-12-31&per_page=20&page=1" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $TOKEN"
```

## 7. Get referral network

`depth` can be 1 through 5. Location filters are optional.

```bash
curl --request GET "$BASE_URL/api/referrals/$REFERRAL_CODE/network?depth=3&district=Pune&taluka=Haveli&city_village=Bavdhan" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $TOKEN"
```

Without location filters:

```bash
curl --request GET "$BASE_URL/api/referrals/$REFERRAL_CODE/network?depth=3" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $TOKEN"
```

## 8. Get volunteer earnings and referral totals

```bash
curl --request GET "$BASE_URL/api/referrals/$REFERRAL_CODE/earnings" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer $TOKEN"
```

## 9. Get banners with the top-three volunteer leaderboard

The response contains the existing `data` banner array and the new `leaderboard` array.

```bash
curl --request GET "$BASE_URL/api/banner" \
  --header "Accept: application/json"
```

## Authorization behavior

Volunteer summary and share-link endpoints are public and do not expose referred-user contact details. Referred users, history, network, and earnings require a Sanctum bearer token. A normal user can request only their own referral code; an admin token can inspect any code.
