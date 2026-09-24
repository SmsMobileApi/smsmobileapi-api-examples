# SMSMobileAPI API examples

Copy-ready examples for connecting business software to a real mobile phone through SMSMobileAPI.

Install the SMSMobileAPI app, connect the phone with its SIM and existing number, then use the API to send and receive SMS, observe call activity, work with WhatsApp, send device notifications and communicate through configured mailboxes.

## Why developers use SMSMobileAPI

- Send SMS through the connected phone and mobile subscription.
- Receive replies from the number customers already know.
- Select a specific connected mobile in a multi-device account.
- Retrieve missed, incoming and outgoing call activity.
- Send and retrieve WhatsApp messages through the supported workflow.
- Trigger real-time automations with signed Webhook V2 events.
- Integrate through plain HTTPS from PHP, Python, Node.js or any backend language.

## Before you start

1. [Create an account](https://smsmobileapi.com/signup/).
2. Install and connect the SMSMobileAPI mobile app.
3. Confirm that the device is online in the dashboard.
4. Copy the API key into a server-side environment variable.
5. Send one test SMS, then store the returned message ID for tracking.

```bash
cp .env.example .env
```

Never commit a real API key. These examples read credentials from environment variables.

## Five-minute SMS quick start

```bash
curl -X POST "https://api.smsmobileapi.com/sendsms/" \
  -d "apikey=$SMSMOBILEAPI_API_KEY" \
  -d "recipients=+15551234567" \
  --data-urlencode "message=Hello from my connected mobile."
```

A successful response confirms that the platform accepted the message. Mobile processing and carrier delivery can happen after the HTTP response.

## Examples by language

| Language | File | Highlights |
| --- | --- | --- |
| cURL | [`examples/curl/quickstart.sh`](examples/curl/quickstart.sh) | Send SMS, read SMS, list calls and synchronize WhatsApp. |
| PHP | [`examples/php/client.php`](examples/php/client.php) | Reusable cURL client with safe POST encoding. |
| Python | [`examples/python/client.py`](examples/python/client.py) | Dependency-free client using `urllib`. |
| Node.js | [`examples/node/client.mjs`](examples/node/client.mjs) | Native `fetch`, Node.js 18 or newer. |

Run a language example with:

```bash
export SMSMOBILEAPI_API_KEY="your-api-key"
export SMSMOBILEAPI_RECIPIENT="+15551234567"

php examples/php/client.php sms:send
python examples/python/client.py sms:received
node examples/node/client.mjs calls:missed
```

## Core endpoints used in this repository

| Capability | Method | Endpoint |
| --- | --- | --- |
| Send an SMS | `POST` | `/sendsms/` |
| List SMS sent through API | `GET` | `/log/sent/sms/` |
| List SMS sent manually from mobile | `GET` | `/log/sent/frommobile/` |
| Retrieve received SMS | `GET` | `/getsms/` |
| List connected mobiles | `GET` | `/gateway/mobile/list/` |
| List missed calls | `GET` | `/call/missed/list/` |
| List incoming calls | `GET` | `/call/incoming/list/` |
| List outgoing calls | `GET` | `/call/outgoing/list/` |
| Send WhatsApp | `POST` | `/sendsms?waonly=yes` |
| Activate WhatsApp retrieval | `GET` | `/getwa/active/` |
| Request WhatsApp synchronization | `GET` | `/getwa/synchronisation/` |
| Retrieve synchronized WhatsApp | `GET` | `/getwa/` |
| Send a mobile notification | `POST` | `/notification/send` |

Base URL: `https://api.smsmobileapi.com`

## WhatsApp receiving: required sequence

Received WhatsApp messages are not collected continuously by default. The privacy-oriented workflow is:

1. Connect WhatsApp from the dashboard.
2. Activate incoming message collection once with `/getwa/active/?statut=1`.
3. Request a time-limited synchronization with `/getwa/synchronisation/`.
4. Wait while the requested synchronization is processed.
5. Retrieve available messages with `/getwa/`.

Request a new synchronization window when you need to collect newer WhatsApp activity.

## Multi-device routing

Call `/gateway/mobile/list/` to retrieve connected device identifiers. When supported by the endpoint, pass `sIdentifiant` to select the mobile that should perform the operation. This makes it possible to separate devices by team, country, SIM, brand or business workflow.

## Reliability rules

- Use international phone-number format.
- Prefer `POST` for message bodies and special characters.
- Set application-level timeouts.
- Retry `429` and temporary `5xx` responses with exponential backoff and jitter.
- Do not blindly retry validation or authentication errors.
- Store returned message GUIDs for correlation.
- Use Webhook V2 for real-time events instead of aggressive polling.
- Keep API keys only on trusted servers.

## Typical business workflows

- CRM: attach received SMS and calls to the correct contact.
- Appointments: send reminders through the existing mobile number.
- E-commerce: send order updates from WooCommerce, Shopify or internal software.
- Support: centralize received messages and missed-call follow-up.
- Operations: route specific actions through specific connected mobiles.
- Automation: trigger workflows from `sms.received`, `call.missed` or `whatsapp.received` webhooks.

## Documentation and tools

- [API documentation](https://smsmobileapi.com/documentations-api-smsmobileapi/)
- [Webhook V2 documentation](https://smsmobileapi.com/webhook/)
- [Dashboard](https://dashboard.smsmobileapi.com/)
- [OpenAPI repository](https://github.com/SmsMobileApi/smsmobileapi-openapi)
- [Postman collection](https://github.com/SmsMobileApi/smsmobileapi-postman)

## Support

Open an issue for an example bug or documentation improvement. Do not include a real API key, phone number, message content or customer data.

Released under the MIT License.
