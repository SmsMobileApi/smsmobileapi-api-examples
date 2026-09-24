#!/usr/bin/env sh
set -eu

: "${SMSMOBILEAPI_API_KEY:?Set SMSMOBILEAPI_API_KEY}"
: "${SMSMOBILEAPI_RECIPIENT:?Set SMSMOBILEAPI_RECIPIENT}"

BASE_URL="https://api.smsmobileapi.com"

echo "Sending one SMS..."
curl --fail-with-body --silent --show-error \
  -X POST "${BASE_URL}/sendsms/" \
  -d "apikey=${SMSMOBILEAPI_API_KEY}" \
  -d "recipients=${SMSMOBILEAPI_RECIPIENT}" \
  --data-urlencode "message=Hello from the SMSMobileAPI cURL quick start."

echo "\nListing unread received SMS..."
curl --fail-with-body --silent --show-error --get \
  "${BASE_URL}/getsms/" \
  --data-urlencode "apikey=${SMSMOBILEAPI_API_KEY}" \
  --data-urlencode "onlyunread=yes"

echo "\nListing missed calls..."
curl --fail-with-body --silent --show-error --get \
  "${BASE_URL}/call/missed/list/" \
  --data-urlencode "apikey=${SMSMOBILEAPI_API_KEY}" \
  --data-urlencode "limit=20"

echo "\nRequesting a WhatsApp synchronization window..."
curl --fail-with-body --silent --show-error --get \
  "${BASE_URL}/getwa/synchronisation/" \
  --data-urlencode "apikey=${SMSMOBILEAPI_API_KEY}"

echo
