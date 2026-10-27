# Firebase push notifications

## Register or refresh a device token

The mobile app should call this endpoint after login and whenever Firebase refreshes the device token.

```bash
curl --request PUT "http://127.0.0.1:8000/api/auth/fcm-token" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --header "Authorization: Bearer YOUR_ACCESS_TOKEN" \
  --data-raw '{
    "fcm_token": "YOUR_FIREBASE_DEVICE_TOKEN"
  }'
```

Successful response:

```json
{
  "success": true,
  "message": "FCM token updated successfully."
}
```

Remove the token during logout:

```bash
curl --request DELETE "http://127.0.0.1:8000/api/auth/fcm-token" \
  --header "Accept: application/json" \
  --header "Authorization: Bearer YOUR_ACCESS_TOKEN"
```

## Send a test push notification

This public testing endpoint sends directly to the supplied FCM token. It does not require an access token, does not replace a user's saved token, and does not create an in-app notification. It can be used even when the global push switch is disabled.

```bash
curl --request POST "http://127.0.0.1:8000/api/notifications/test-push" \
  --header "Accept: application/json" \
  --header "Content-Type: application/json" \
  --data-raw '{
    "fcm_token": "YOUR_FIREBASE_DEVICE_TOKEN",
    "title": "Mali Setu Test Notification",
    "message": "Firebase push notifications are working correctly.",
    "data": {
      "screen": "home",
      "source": "api_test"
    }
  }'
```

Successful response:

```json
{
  "success": true,
  "message": "Test push notification sent successfully.",
  "data": {
    "firebase_message_name": "projects/malisetu-live/messages/0:123456789"
  }
}
```

## Admin workflow

Open **Admin > Push Notifications** to:

- enable or disable Firebase push globally;
- upload or replace the Firebase service-account JSON;
- enable or disable an individual notification action;
- edit its title, message, deep link, and variables;
- see a live mobile-style preview while editing;
- create a custom action; and
- send an enabled action to one user or all users with registered device tokens.

An action must be enabled and have **Send Firebase push** enabled. Existing application events use the saved action content instead of hard-coded push content.

## Mobile payload

Firebase receives both a `notification` block for standard OS display and a `data` block for app navigation. The data block includes:

- `notification_id`
- `action_key`
- `action_url`
- the event-specific data configured by the application/admin

The mobile app should navigate to `action_url` when the user taps the notification. Foreground notifications must be displayed by the app's Firebase message handler; background display is handled by Android/iOS from the `notification` block.
