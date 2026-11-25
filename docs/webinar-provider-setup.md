# Agora primary + Daily.co fallback setup

Each webinar stores its selected provider. The admin create/edit form displays **Agora — Working/Not working** and **Daily.co — Working/Not working** from the configured server credentials. Agora remains the default when both are configured; the admin can select Daily.co for any webinar.

After changing `.env`, run:

```bash
php artisan optimize:clear
php artisan migrate
```

## Agora keys

Required keys:

- `AGORA_APP_ID`
- `AGORA_APP_CERTIFICATE`
- Optional: `AGORA_TOKEN_EXPIRATION=3600`

Generation steps:

1. Sign in to the Agora Console at `https://console.agora.io/`.
2. Open **Project Management** and create a project.
3. Choose the secured App ID + App Certificate/token authentication mode.
4. Copy the project's **App ID**.
5. Open the project's security/configuration page, enable the **Primary Certificate** if required, and copy the **App Certificate**.
6. Put both values only in the server `.env`; never expose the App Certificate in a browser/mobile app or source control.

```dotenv
AGORA_APP_ID=your_agora_app_id
AGORA_APP_CERTIFICATE=your_agora_app_certificate
AGORA_TOKEN_EXPIRATION=3600
```

The server creates short-lived RTC tokens. Clients receive only the App ID, channel, user account and short-lived token.

## Daily.co key

Required key:

- `DAILY_API_KEY`

Generation steps:

1. Sign in to the Daily dashboard at `https://dashboard.daily.co/`.
2. Open **Developers** (or **API keys**, depending on the dashboard navigation).
3. Create/copy a Daily REST API key.
4. Store it only in the server `.env`; do not put it in frontend JavaScript or a mobile app.

```dotenv
DAILY_API_KEY=your_daily_rest_api_key
DAILY_API_URL=https://api.daily.co/v1
DAILY_API_TIMEOUT=15
```

When a Daily webinar is first started, the server creates a private room and returns a short-lived meeting token. Host tokens use Daily owner privileges; attendee tokens are participant tokens. The same web join page, camera/microphone, screen sharing, participant count, Q&A, access control, registration checks and scheduled join window remain in use.

## Server scheduler (birthday wishes)

The birthday command runs every day at 08:00 in the application timezone. The server must run Laravel's scheduler:

```cron
* * * * * cd /path/to/mali_setu && php artisan schedule:run >> /dev/null 2>&1
```

You can test it manually:

```bash
php artisan notifications:send-birthday-wishes
```
