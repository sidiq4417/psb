/**
 * Alert - Utility class for displaying CoreUI-based toast notifications.
 * Uses inline CoreUI SVG icons for flexibility without external asset dependencies.
 *
 * @since 2025
 * @author antonsofyan@president.ac.id
 */
class Alert {

    /**
     * Default options applied to every alert unless overridden.
     * @type {{delay:number, autohide:boolean, position:string, iconSize:number}}
     */
    static defaultOptions = {
        delay: 8000, // Duration alert stays visible (ms)
        autohide: true, // Whether alert hides automatically
        position: "top-0 end-0", // Position of alert container (Bootstrap utility classes)
        iconSize: 24, // Default icon size in pixels
        onShow: null,
        onHide: null
    };

    /**
     * Inline SVG icons from CoreUI (free set).
     * Each property contains the full <svg> markup string.
     * @type {Record<string,string>}
     */
    static icons = {
        success: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="var(--ci-primary-color, currentColor)" d="M426.072,86.928A238.75,238.75,0,0,0,88.428,424.572,238.75,238.75,0,0,0,426.072,86.928ZM257.25,462.5c-114,0-206.75-92.748-206.75-206.75S143.248,49,257.25,49,464,141.748,464,255.75,371.252,462.5,257.25,462.5Z" class="ci-primary"/><polygon fill="var(--ci-primary-color, currentColor)" points="221.27 305.808 147.857 232.396 125.23 255.023 221.27 351.063 388.77 183.564 366.142 160.937 221.27 305.808" class="ci-primary"/></svg>`,
        error: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><polygon fill="var(--ci-primary-color, currentColor)" points="348.071 141.302 260.308 229.065 172.545 141.302 149.917 163.929 237.681 251.692 149.917 339.456 172.545 362.083 260.308 274.32 348.071 362.083 370.699 339.456 282.935 251.692 370.699 163.929 348.071 141.302" class="ci-primary"/><path fill="var(--ci-primary-color, currentColor)" d="M425.706,86.294A240,240,0,0,0,86.294,425.706,240,240,0,0,0,425.706,86.294ZM256,464C141.309,464,48,370.691,48,256S141.309,48,256,48s208,93.309,208,208S370.691,464,256,464Z" class="ci-primary"/></svg>`,
        warning: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect width="32" height="176" x="240" y="176" fill="var(--ci-primary-color, currentColor)" class="ci-primary"/><rect width="32" height="32" x="240" y="384" fill="var(--ci-primary-color, currentColor)" class="ci-primary"/><path fill="var(--ci-primary-color, currentColor)" d="M274.014,16H237.986L16,445.174V496H496V445.174ZM464,464H48V452.959L256,50.826,464,452.959Z" class="ci-primary"/></svg>`,
        info: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><rect width="34.924" height="34.924" x="256" y="95.998" fill="var(--ci-primary-color, currentColor)" class="ci-primary"/><path fill="var(--ci-primary-color, currentColor)" d="M16,496H496V16H16ZM48,48H464V464H48Z" class="ci-primary"/><path fill="var(--ci-primary-color, currentColor)" d="M285.313,359.032a18.123,18.123,0,0,1-15.6,8.966,18.061,18.061,0,0,1-17.327-23.157l35.67-121.277A49.577,49.577,0,0,0,194.7,190.572l-11.718,28.234,29.557,12.266,11.718-28.235a17.577,17.577,0,0,1,33.1,11.7l-35.67,121.277A50.061,50.061,0,0,0,269.709,400a50.227,50.227,0,0,0,43.25-24.853l15.1-25.913-27.646-16.115Z" class="ci-primary"/></svg>`,
        default: `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="var(--ci-primary-color, currentColor)" d="M450.27,348.569,406.6,267.945V184c0-83.813-68.187-152-152-152s-152,68.187-152,152v83.945L58.928,348.568A24,24,0,0,0,80.031,384h86.935c-.238,2.636-.367,5.3-.367,8a88,88,0,0,0,176,0c0-2.7-.129-5.364-.367-8h86.935a24,24,0,0,0,21.1-35.431ZM310.6,392a56,56,0,1,1-111.419-8H310.018A56.14,56.14,0,0,1,310.6,392ZM93.462,352,134.6,276.055V184a120,120,0,0,1,240,0v92.055L415.736,352Z" class="ci-primary"/></svg>`
    };

    /**
     * Background color classes for each alert type.
     * @type {Record<string,string>}
     */
    static colors = {
        success: "text-bg-success text-white",
        error: "text-bg-danger text-white",
        warning: "text-bg-warning text-white",
        info: "text-bg-info text-white",
        default: "text-bg-light text-dark"
    };

    /**
     * Creates or retrieves the alert container element for a given position.
     * @param {string} position - Position classes (e.g. "top-0 end-0").
     * @returns {HTMLDivElement} - The alert container element.
     */
    static getContainer(position) {
        const id = `alert-container`;
        let container = document.getElementById(id);
        if (!container) {
            container = document.createElement("div");
            container.id = id;
            container.className = `alert-container position-fixed ${position} p-3`;
            document.body.appendChild(container);
        }
        return container;
    }

    /**
     * Creates and shows a new alert notification.
     * @param {"success"|"error"|"warning"|"info"|"default"} type - Type of alert.
     * @param {string} message - Message text displayed inside the alert.
     * @param {object} [options] - Optional overrides for defaultOptions.
     */
    static createAlert(type, message, options = {}) {
        const opts = {...Alert.defaultOptions, ...options};
        const container = Alert.getContainer(opts.position);

        const color = Alert.colors[type] || Alert.colors.info;
        const iconStr = Alert.icons[type] || Alert.icons.info;

        const alertEl = document.createElement("div");
        alertEl.className = `toast align-items-center ${color} fw-medium border-0 mb-2`;
        alertEl.setAttribute("role", "alert");
        alertEl.setAttribute("aria-live", "assertive");
        alertEl.setAttribute("aria-atomic", "true");

        // Outer flex container: body + close button
        const outer = document.createElement("div");
        outer.className = "d-flex";

        // Body container
        const body = document.createElement("div");
        body.className = "toast-body d-flex align-items-center";

        // Icon wrapper
        const iconWrapper = document.createElement("span");
        iconWrapper.className = "c-icon me-3";
        iconWrapper.style.width = `${opts.iconSize}px`;
        iconWrapper.style.height = `${opts.iconSize}px`;
        iconWrapper.innerHTML = iconStr.replace(
            /(<svg\b[^>]*)(>)/i,
            `$1 width="${opts.iconSize}" height="${opts.iconSize}"$2`
        );

        // Message node with wrapping fix
        const msgNode = document.createElement("span");
        msgNode.textContent = String(message);

        // Close button
        const closeBtn = document.createElement("button");
        closeBtn.type = "button";
        closeBtn.className = `btn-close ${type === 'default' ? 'btn-close-dark' : 'btn-close-white'} me-3 m-auto`;
        closeBtn.setAttribute("data-coreui-dismiss", "toast");
        closeBtn.setAttribute("aria-label", "Close");

        body.appendChild(iconWrapper);
        body.appendChild(msgNode);
        outer.appendChild(body);
        outer.appendChild(closeBtn);
        alertEl.appendChild(outer);

        container.appendChild(alertEl);

        // Initialize CoreUI Toast
        // noinspection JSUnresolvedReference
        const instance = new coreui.Toast(alertEl, {
            autohide: opts.autohide,
            delay: opts.delay
        });

        if (typeof opts.onShow === "function") {
            alertEl.addEventListener("shown.coreui.toast", opts.onShow);
        }
        if (typeof opts.onHide === "function") {
            alertEl.addEventListener("hidden.coreui.toast", opts.onHide);
        }

        instance.show();

        // Cleanup
        alertEl.addEventListener("hidden.coreui.toast", () => {
            alertEl.remove();
            if (container.querySelectorAll(".toast").length === 0) {
                container.remove();
            }
        });
    }

    /** Shortcut for success alert */
    static success(msg, opts = {}) {
        Alert.createAlert("success", msg, opts);
    }

    /** Shortcut for error alert */
    static error(msg, opts = {}) {
        Alert.createAlert("error", msg, opts);
    }

    /** Shortcut for warning alert */
    static warning(msg, opts = {}) {
        Alert.createAlert("warning", msg, opts);
    }

    /** Shortcut for info alert */
    static info(msg, opts = {}) {
        Alert.createAlert("info", msg, opts);
    }

    /** Shortcut for default alert */
    static default(msg, opts = {}) {
        Alert.createAlert("default", msg, opts);
    }
}