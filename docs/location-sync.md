# Location master synchronization

The admin location pages are read-only. Use **Sync States**, **Sync Districts**, **Sync Talukas**, and **Sync Cities** to insert new records and update existing records by their stable LGD key.

## Provider and credentials

The default provider is `https://lgd-json-api.vercel.app/api`, a keyless JSON API derived from the Government of India Local Government Directory (LGD). No account or API key is required for this provider.

Add these values to the production `.env`:

```dotenv
LOCATION_API_BASE_URL=https://lgd-json-api.vercel.app/api
LOCATION_API_KEY=
LOCATION_API_TIMEOUT=30
```

Then refresh cached configuration:

```bash
php artisan config:clear
```

If a compatible private provider is adopted later, set its base URL and token in `LOCATION_API_BASE_URL` and `LOCATION_API_KEY`. The token is sent as `Authorization: Bearer <token>`; obtain it from that provider's developer console. The default provider does not issue or need a key.

## Deployment

Run the database migration:

```bash
php artisan migrate --force
```

The migration creates the `states` table, adds external LGD keys and sync timestamps, and preserves all old rows under an **Unassigned / Legacy** state. Synchronization only inserts or updates; it does not delete existing records.

Run the sync pages in hierarchy order for the clearest progress: states, districts, talukas, then cities. District/taluka/city sync also ensures required parent records exist.

## Mobile/API usage

The local application API exposes synchronized records and carries `state_key` throughout the hierarchy:

```text
GET /api/location/states
GET /api/location/districts?state_key=27
GET /api/location/talukas?state_key=27&district_key=490
GET /api/location/cities?state_key=27&district_key=490
GET /api/location/cities?state_key=27&district_key=490&taluka_key=...
```

Responses include `state_key`, `district_key`, `taluka_key`, and `city_key` as applicable. LGD city/urban-local-body data does not always identify a district or sub-district. When a district is supplied, the city is grouped under that district's generated **Urban areas** taluka. Otherwise it is preserved under a generated **State-level urban areas** district and its **Urban areas** taluka.

Data source: Ministry of Panchayati Raj, Government of India — Local Government Directory (LGD), via the configured LGD-derived API.
