var H = H || {};

H.Btn = (label, title, event, direction) => {
    direction = direction || 'bottom';
    return `<button onclick="${event}" class="btn btn-light shadow-none" data-coreui-toggle="tooltip" data-coreui-placement="${direction}" title="${title}">${label}</button>`;
};

H.BtnDanger = (label, title, event, direction) => {
    direction = direction || 'bottom';
    return `<button onclick="${event}" class="btn btn-danger shadow-none" data-coreui-toggle="tooltip" data-coreui-placement="${direction}" title="${title}">${label}</button>`;
};

H.BtnIcon = (icon, title, event, direction) => {
    direction = direction || 'bottom';
    return `<button onclick="${event}" class="btn btn-light text-body btn-sm" data-coreui-toggle="tooltip" data-coreui-placement="${direction}" title="${title}"><i class="cil-${icon}"></i></button>`;
};

H.LinkIcon = (icon, title, url, direction) => {
    direction = direction || 'bottom';
    return `<a href="${url}" class="btn btn-light text-body btn-sm" data-coreui-toggle="tooltip" data-coreui-placement="${direction}" title="${title}"><i class="cil-${icon}"></i></a>`;
};

H.StrToObject = (str) => {
    if ('object' == typeof str) return str;
    if ('string' != typeof str) {
        throw ('cannot parse non-json-string')
    }
    if (str === '') return str;
    try {
        return JSON.parse(str);
    } catch (e) {
        console.error('H.StrToObject: invalid JSON', e);
        return null;
    }
};

/**
 * Escape a value for safe insertion into HTML markup or attributes.
 * @param {*} value - Any value; null/undefined become an empty string.
 * @returns {string}
 */
H.Esc = (value) => {
    if (value === null || value === undefined) return '';
    return String(value)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
};

/**
 * Read the CSRF token rendered by csrf_meta() in the page head.
 * @returns {string|null}
 */
H.CsrfToken = () => {
    const meta = document.querySelector('meta[name="X-CSRF-TOKEN"]');
    return meta ? meta.getAttribute('content') : null;
};

/*
 * Attach the CSRF token to every same-origin, state-changing request made with
 * fetch() or XMLHttpRequest, so individual views do not have to remember it.
 */
(() => {
    const SAFE = ['GET', 'HEAD', 'OPTIONS'];
    const sameOrigin = (url) => {
        try {
            return new URL(url, window.location.href).origin === window.location.origin;
        } catch (e) {
            return false;
        }
    };

    if (window.fetch && !window.fetch.__csrf) {
        const nativeFetch = window.fetch.bind(window);
        const patched = (input, init = {}) => {
            const url = input instanceof Request ? input.url : String(input);
            const method = ((init && init.method) || (input instanceof Request ? input.method : 'GET')).toUpperCase();
            const token = H.CsrfToken();
            if (token && !SAFE.includes(method) && sameOrigin(url)) {
                const headers = new Headers((init && init.headers) || (input instanceof Request ? input.headers : undefined));
                if (!headers.has('X-CSRF-TOKEN')) headers.set('X-CSRF-TOKEN', token);
                if (!headers.has('X-Requested-With')) headers.set('X-Requested-With', 'XMLHttpRequest');
                init = Object.assign({}, init, {headers});
            }
            return nativeFetch(input, init);
        };
        patched.__csrf = true;
        window.fetch = patched;
    }

    if (window.XMLHttpRequest && !XMLHttpRequest.prototype.__csrf) {
        const open = XMLHttpRequest.prototype.open;
        const send = XMLHttpRequest.prototype.send;
        XMLHttpRequest.prototype.open = function (method, url) {
            this.__csrfMethod = String(method || 'GET').toUpperCase();
            this.__csrfUrl = url;
            return open.apply(this, arguments);
        };
        XMLHttpRequest.prototype.send = function () {
            const token = H.CsrfToken();
            if (token && !SAFE.includes(this.__csrfMethod) && sameOrigin(this.__csrfUrl)) {
                try { this.setRequestHeader('X-CSRF-TOKEN', token); } catch (e) { /* already sent */ }
            }
            return send.apply(this, arguments);
        };
        XMLHttpRequest.prototype.__csrf = true;
    }
})();

H.Currency = (number, prefix = "", sep = " ") => {
    let numStr = number.toString();
    return prefix + numStr.replace(/\B(?=(\d{3})+(?!\d))/g, sep);
};

H.FormatDate = (dateString) => {
    if (! dateString) return '';
    const months = [
        "Januari", "Februari", "Maret", "April", "Mei", "Juni",
        "Juli", "Agustus", "September", "Oktober", "November", "Desember"
    ];
    const [year, month, day] = dateString.split('-');
    const monthName = months[parseInt(month) - 1];
    return `${parseInt(day)} ${monthName} ${year}`;
};

H.Now = () => {
    const today = new Date();
    const year = today.getFullYear();
    const month = String(today.getMonth() + 1).padStart(2, '0');
    const day = String(today.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
};

/**
 * Show or hide a full‑screen loading overlay with a spinner.
 * @param {boolean} [show=true] - True to show the overlay, false to hide it.
 * @returns {void}
 */
H.Loading = (show = true) => {
    const overlayId = 'cui-loading-overlay';
    let overlay = document.getElementById(overlayId);

    if (show) {
        if (!overlay) {
            overlay = document.createElement('div');
            overlay.id = overlayId;
            overlay.style.cssText = `
                    position: fixed; top:0; left:0; width:100%; height:100%;
                    background: rgba(0,0,0,0.7); display:flex;
                    align-items:center; justify-content:center; z-index:9999;
                `;
            overlay.innerHTML = `<span class="loader"></span>`;
            document.body.appendChild(overlay);

            if (!document.getElementById('loader-style')) {
                const style = document.createElement('style');
                style.id = 'loader-style';
                style.textContent = `
                        .loader {
                          width: 128px; height: 128px; border-radius: 50%;
                          border-top: 4px solid #FFF; border-right: 4px solid transparent;
                          animation: rotation 1s linear infinite; position: relative;
                        }
                        .loader::after {
                          content: ''; position: absolute; left:0; top:0;
                          width: 128px; height: 128px; border-radius: 50%;
                          border-left: 4px solid #FF3D00; border-bottom: 4px solid transparent;
                          animation: rotation 0.5s linear infinite reverse;
                        }
                        @keyframes rotation {
                          0% { transform: rotate(0deg); }
                          100% { transform: rotate(360deg); }
                        }
                    `;
                document.head.appendChild(style);
            }
        }
    } else {
        if (overlay) overlay.remove();
    }
};

/**
 * Reusable AJAX function using Fetch API (async/await).
 * Supports GET, POST, PUT, DELETE with query parameters, JSON body, and custom headers.
 * @param {string} url - Target endpoint.
 * @param {object} [options={}] - Request options.
 * @param {string} [options.method="GET"] - HTTP method.
 * @param {object} [options.params] - Query string parameters.
 * @param {object} [options.body] - JSON body for POST/PUT requests.
 * @param {object} [options.headers] - Custom headers.
 * @returns {Promise<any>} Resolves with JSON or text response.
 * @throws {Error} If the HTTP response status is not OK.
 */
H.AjaxRequest = async function (url, options = {}) {
    const {
        method = "POST",
        params = null,
        body = null,
        headers = {}
    } = options;
    let query = "";
    if (params && typeof params === "object") {
        query = "?" + new URLSearchParams(params).toString();
    }

    const fetchOptions = {
        method,
        headers: {...headers}
    };

    if (body) {
        if (body instanceof FormData) {
            fetchOptions.body = body;
        } else {
            fetchOptions.body = JSON.stringify(body);
            fetchOptions.headers["Content-Type"] = "application/json";
        }
    }

    try {
        H.Loading(true);
        const response = await fetch(url + query, fetchOptions);

        if (!response.ok) {
            throw new Error(`HTTP error! Status: ${response.status}`);
        }

        const contentType = response.headers.get("content-type");
        if (contentType && contentType.includes("application/json")) {
            return await response.json();
        } else {
            return await response.text();
        }
    } catch (error) {
        console.error("AJAX Request Error:", error);
        throw error;
    } finally {
        H.Loading(false);
    }
}

/**
 * Sidebar active state: match nav-links by URL prefix.
 * Add `data-coreui-url-match="path1,path2"` to a nav-link
 * to keep it active on any of those URL prefixes.
 */
function applySidebarActiveMatch() {
    const currentPath = location.pathname.replace(/\/+$/, '');
    document.querySelectorAll('[data-coreui-url-match]').forEach(link => {
        const prefixes = link.getAttribute('data-coreui-url-match').split(',');
        if (prefixes.some(prefix => {
            const fullPath = '/' + prefix.trim().replace(/^\/+|\/+$/g, '');
            return currentPath === fullPath || currentPath.startsWith(fullPath + '/');
        })) {
            link.classList.add('active');
        }
    });
}

document.addEventListener("DOMContentLoaded", () => {
    const sidebar = document.querySelector(".sidebar");
    const toggler = document.querySelector(".sidebar-toggler");
    if (!sidebar || !toggler) return;

    applySidebarActiveMatch();

    let savedState = window.localStorage.getItem("sidebarState");

    if (!savedState) {
        savedState = "narrow";
        window.localStorage.setItem("sidebarState", "narrow");
    }

    if (savedState === "narrow") {
        sidebar.classList.add("sidebar-narrow-unfoldable");
    } else {
        sidebar.classList.remove("sidebar-narrow-unfoldable");
    }

    toggler.addEventListener("click", () => {
        const isNarrow = sidebar.classList.toggle("sidebar-narrow-unfoldable");
        window.localStorage.setItem("sidebarState", isNarrow ? "narrow" : "wide");
    });
});