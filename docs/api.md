# JSON API

Base entrypoint: `public/api.php`

## Endpoints

- `GET /api/dives`
  - Response: `{ "data": [1, 2, 3, ...] }`
- `GET /api/dives/{number}`
  - Response: `{ "data": { "number": 1, "logId": 10, "placeId": 100, "dateTime": "...", "depthMax": 20.5 } }`
- `GET /api/stats`
  - Response: `{ "data": { "diveCount": 2, "maxDepth": 25.5, "avgDepth": 23.0, "totalDurationMinutes": 90 } }`

## Errors

Errors use:

```json
{
  "error": {
    "code": "not_found",
    "message": "Resource not found"
  }
}
```

Unknown resources or IDs return HTTP 404.
