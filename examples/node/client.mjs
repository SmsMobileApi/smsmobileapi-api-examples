const baseUrl = "https://api.smsmobileapi.com";

function required(name) {
  const value = (process.env[name] || "").trim();
  if (!value) throw new Error(`Missing environment variable: ${name}`);
  return value;
}

async function request(method, path, parameters) {
  let url = `${baseUrl}${path}`;
  const options = { method, headers: { accept: "application/json" } };
  const form = new URLSearchParams(parameters);
  if (method === "GET") {
    url += `?${form}`;
  } else {
    options.headers["content-type"] = "application/x-www-form-urlencoded";
    options.body = form;
  }
  const response = await fetch(url, options);
  const text = await response.text();
  if (!response.ok) throw new Error(`HTTP ${response.status}: ${text}`);
  return JSON.parse(text);
}

const apiKey = required("SMSMOBILEAPI_API_KEY");
const command = process.argv[2] || "sms:send";
const commands = {
  "sms:send": ["POST", "/sendsms/", {
    apikey: apiKey,
    recipients: required("SMSMOBILEAPI_RECIPIENT"),
    message: "Hello from the SMSMobileAPI Node.js example.",
  }],
  "sms:received": ["GET", "/getsms/", { apikey: apiKey, onlyunread: "yes" }],
  "calls:missed": ["GET", "/call/missed/list/", { apikey: apiKey, limit: "20" }],
  "devices:list": ["GET", "/gateway/mobile/list/", { apikey: apiKey }],
  "whatsapp:synchronize": ["GET", "/getwa/synchronisation/", { apikey: apiKey }],
};

if (!commands[command]) throw new Error(`Unknown command: ${command}`);
console.log(JSON.stringify(await request(...commands[command]), null, 2));
