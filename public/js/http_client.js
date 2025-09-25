/*
  http-client.js
  Thư viện tiện ích gọi HTTP bằng JavaScript thuần (Fetch API).

  Tính năng:
  - Wrapper cho fetch với timeout (AbortController)
  - Hỗ trợ GET/POST/PUT/PATCH/DELETE
  - Hỗ trợ query params, JSON body, FormData
  - Retry với exponential backoff
  - Interceptors request/response (như axios)
  - Xử lý lỗi có cấu trúc (HttpError)
  - Promise-based, nhẹ và dễ tuỳ chỉnh

  Example sử dụng (xem phần cuối file):
    const client = new HttpClient({ baseURL: 'https://api.example.com', timeout: 8000 });
    client.get('/users', { params: { limit: 10 } })
      .then(resp => console.log(resp.data))
      .catch(err => console.error(err));
*/

class HttpError extends Error {
  constructor(message, { status = null, statusText = null, url = null, data = null } = {}) {
    super(message);
    this.name = 'HttpError';
    this.status = status;
    this.statusText = statusText;
    this.url = url;
    this.data = data;
  }
}

class HttpClient {
  /**
   * @param {Object} options
   * @param {string} [options.baseURL]
   * @param {Object} [options.headers]
   * @param {number} [options.timeout] - milliseconds
   * @param {number} [options.retries] - default 0
   * @param {function(number):number} [options.backoff] - function(retryIndex) => ms
   */
  constructor({ baseURL = '', headers = {}, timeout = 0, retries = 0, backoff = null } = {}) {
    this.baseURL = baseURL;
    this.defaultHeaders = Object.assign({ 'Accept': 'application/json' }, headers);
    this.timeout = timeout;
    this.retries = retries;
    this.backoff = typeof backoff === 'function' ? backoff : (attempt => 100 * Math.pow(2, attempt));

    // interceptors: arrays of functions
    // request interceptors: (config) => config | Promise<config>
    // response interceptors: (response) => response | Promise<response>
    this.requestInterceptors = [];
    this.responseInterceptors = [];
  }

  // Add request interceptor
  addRequestInterceptor(fn) { this.requestInterceptors.push(fn); return this; }
  // Add response interceptor
  addResponseInterceptor(fn) { this.responseInterceptors.push(fn); return this; }

  // Helper to build URL with query params
  _buildUrl(path, params) {
    const url = path.startsWith('http://') || path.startsWith('https://') ? path : (this.baseURL ? `${this.baseURL.replace(/\/$/, '')}/${path.replace(/^\//, '')}` : path);
    if (!params) return url;
    const search = new URLSearchParams();
    Object.keys(params).forEach(key => {
      const val = params[key];
      if (val === undefined || val === null) return;
      if (Array.isArray(val)) {
        val.forEach(v => search.append(key, v));
      } else if (typeof val === 'object') {
        search.append(key, JSON.stringify(val));
      } else {
        search.append(key, String(val));
      }
    });
    const qs = search.toString();
    return qs ? `${url}${url.includes('?') ? '&' : '?'}${qs}` : url;
  }

  // normalize config and run request interceptors
  async _applyRequestInterceptors(config) {
    let current = config;
    for (const fn of this.requestInterceptors) {
      // interceptor can be sync or async
      // allow it to modify or replace config
      // if it returns undefined, keep current
      const result = await fn(current) ;
      if (result !== undefined) current = result;
    }
    return current;
  }

  // apply response interceptors
  async _applyResponseInterceptors(resp) {
    let current = resp;
    for (const fn of this.responseInterceptors) {
      const result = await fn(current);
      if (result !== undefined) current = result;
    }
    return current;
  }

  // core request method
  async request(method, path, { params = null, headers = {}, body = null, timeout = null, retries = null, raw = false } = {}) {
    let url = this._buildUrl(path, params);
    const mergedHeaders = Object.assign({}, this.defaultHeaders, headers || {});

    const config = { method: method.toUpperCase(), url, headers: mergedHeaders, body, timeout: timeout == null ? this.timeout : timeout };

    // run request interceptors
    const finalConfig = await this._applyRequestInterceptors(config);

    // destructure final config
    url = finalConfig.url;
    const finalHeaders = finalConfig.headers || {};
    const finalBody = finalConfig.body;
    const finalTimeout = finalConfig.timeout || 0;

    // Prepare fetch options
    const fetchOptions = { method: finalConfig.method, headers: finalHeaders };

    // handle body and headers
    if (finalBody != null) {
      if (finalBody instanceof FormData || finalBody instanceof URLSearchParams || finalBody instanceof Blob) {
        fetchOptions.body = finalBody;
        // do not set content-type; browser will set boundary
      } else if (typeof finalBody === 'object' && !(finalBody instanceof ArrayBuffer)) {
        // JSON
        fetchOptions.body = JSON.stringify(finalBody);
        if (!('Content-Type' in finalHeaders)) fetchOptions.headers = Object.assign({ 'Content-Type': 'application/json' }, finalHeaders);
      } else {
        // string or other
        fetchOptions.body = finalBody;
      }
    }

    // Implement timeout with AbortController
    const controller = new AbortController();
    fetchOptions.signal = controller.signal;
    let timeoutId;
    if (finalTimeout > 0) {
      timeoutId = setTimeout(() => controller.abort(), finalTimeout);
    }

    const maxRetries = retries == null ? this.retries : retries;
    let attempt = 0;
    let lastError = null;

    while (attempt <= maxRetries) {
      try {
        const res = await fetch(url, fetchOptions);
        if (timeoutId) clearTimeout(timeoutId);

        const contentType = res.headers.get('Content-Type') || '';
        let data = null;

        if (raw) {
          data = res.body; // readable stream
        } else if (contentType.includes('application/json')) {
          data = await res.json().catch(() => null);
        } else if (contentType.includes('text/')) {
          data = await res.text().catch(() => null);
        } else {
          // fallback: try arrayBuffer then text
          try { data = await res.arrayBuffer(); } catch (e) { try { data = await res.text(); } catch (e2) { data = null; } }
        }

        const response = { ok: res.ok, status: res.status, statusText: res.statusText, headers: res.headers, data, url };

        // if non-2xx, throw HttpError to be catchable and possibly retried
        if (!res.ok) {
          const err = new HttpError(`HTTP error: ${res.status} ${res.statusText}`, { status: res.status, statusText: res.statusText, url, data });
          throw err;
        }

        // run response interceptors
        const finalResponse = await this._applyResponseInterceptors(response);

        return finalResponse;
      } catch (err) {
        lastError = err;
        // if aborted due to timeout, wrap with HttpError for uniformity
        if (err.name === 'AbortError') {
          lastError = new HttpError('Request timeout', { url });
        }

        // decide whether to retry: network errors or 5xx or custom HttpError without status
        const isNetworkError = !(lastError instanceof HttpError) || lastError.status === null;
        const isServerError = lastError instanceof HttpError && lastError.status >= 500 && lastError.status < 600;

        if (attempt < maxRetries && (isNetworkError || isServerError)) {
          const wait = this.backoff(attempt);
          await new Promise(r => setTimeout(r, wait));
          attempt += 1;
          // reset timeout controller for next attempt
          if (finalTimeout > 0) {
            if (timeoutId) clearTimeout(timeoutId);
            // create new controller for next attempt
          }
          continue;
        }

        // no more retries
        throw lastError;
      }
    }

    // if we exit loop unexpectedly
    throw lastError || new HttpError('Unknown request error');
  }

  // convenience methods
  get(path, opts = {}) { return this.request('GET', path, opts); }
  post(path, body = null, opts = {}) { return this.request('POST', path, Object.assign({}, opts, { body })); }
  put(path, body = null, opts = {}) { return this.request('PUT', path, Object.assign({}, opts, { body })); }
  patch(path, body = null, opts = {}) { return this.request('PATCH', path, Object.assign({}, opts, { body })); }
  delete(path, opts = {}) { return this.request('DELETE', path, opts); }
}

// === Example usage ===

/*
const client = new HttpClient({
  baseURL: 'https://jsonplaceholder.typicode.com',
  timeout: 8000,
  retries: 2,
});

// add an auth token automatically
client.addRequestInterceptor(async (config) => {
  const token = localStorage.getItem('token');
  if (token) {
    config.headers = Object.assign({}, config.headers, { Authorization: `Bearer ${token}` });
  }
  return config;
});

// handle certain response shapes
client.addResponseInterceptor((response) => {
  // if API wraps data in { success: true, payload: ... }
  if (response.data && response.data.payload !== undefined) {
    return Object.assign({}, response, { data: response.data.payload });
  }
  return response;
});

(async () => {
  try {
    const res = await client.get('/posts', { params: { userId: 1 } });
    console.log('Status:', res.status);
    console.log('Data:', res.data);
  } catch (e) {
    console.error('Request failed:', e);
  }
})();
*/

// Export for module systems
if (typeof module !== 'undefined' && typeof module.exports !== 'undefined') {
  module.exports = { HttpClient, HttpError };
} else {
  window.HttpClient = HttpClient;
  window.HttpError = HttpError;
}
