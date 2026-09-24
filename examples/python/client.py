#!/usr/bin/env python3
"""Dependency-free command-line examples for the SMSMobileAPI REST API."""

from __future__ import annotations

import json
import os
import sys
import urllib.error
import urllib.parse
import urllib.request

BASE_URL = "https://api.smsmobileapi.com"


def required(name: str) -> str:
    value = os.getenv(name, "").strip()
    if not value:
        raise SystemExit(f"Missing environment variable: {name}")
    return value


def request(method: str, path: str, parameters: dict[str, object]) -> object:
    encoded = urllib.parse.urlencode(parameters).encode()
    url = BASE_URL + path
    data = None
    headers = {"Accept": "application/json"}
    if method.upper() == "GET":
        url += "?" + encoded.decode()
    else:
        data = encoded
        headers["Content-Type"] = "application/x-www-form-urlencoded"
    try:
        with urllib.request.urlopen(urllib.request.Request(url, data=data, headers=headers, method=method), timeout=30) as response:
            body = response.read().decode("utf-8")
    except urllib.error.HTTPError as error:
        raise SystemExit(f"HTTP {error.code}: {error.read().decode('utf-8', 'replace')}") from error
    return json.loads(body)


api_key = required("SMSMOBILEAPI_API_KEY")
command = sys.argv[1] if len(sys.argv) > 1 else "sms:send"

commands = {
    "sms:send": ("POST", "/sendsms/", {
        "apikey": api_key,
        "recipients": required("SMSMOBILEAPI_RECIPIENT"),
        "message": "Hello from the SMSMobileAPI Python example.",
    }),
    "sms:received": ("GET", "/getsms/", {"apikey": api_key, "onlyunread": "yes"}),
    "calls:missed": ("GET", "/call/missed/list/", {"apikey": api_key, "limit": 20}),
    "devices:list": ("GET", "/gateway/mobile/list/", {"apikey": api_key}),
    "whatsapp:synchronize": ("GET", "/getwa/synchronisation/", {"apikey": api_key}),
}

if command not in commands:
    raise SystemExit(f"Unknown command: {command}. Available: {', '.join(commands)}")

print(json.dumps(request(*commands[command]), indent=2, ensure_ascii=False))
