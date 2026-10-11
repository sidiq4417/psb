/**
 * Dialog.js
 * ==========
 * A lightweight utility class for displaying modal Dialog (alert, confirm, prompt, notify, loading)
 * using vanilla JavaScript and CoreUI.
 *
 * ## Dependencies
 * - CoreUI (CSS & JS) — ensure `coreui.Modal` is available globally.
 * - Icon set (CoreUI Icons, Bootstrap Icons, Font Awesome, etc.) — adjust icon class names accordingly.
 *
 * ## Features
 * - Promise-based APIs for async/await usage.
 * - Configurable defaults via `Dialog.setDefaults()`.
 * - Theme mapping for common dialog types (success, danger, error, warning, info).
 * - Auto-close support with countdown indicator (for notify).
 * - Extensible options: all methods accept either simple parameters or a full options object (Hybrid API).
 *
 * ## Methods
 * - `setDefaults(newDefaults)` — Update global defaults for all Dialog.
 * - `_renderModal(options)` — Internal helper to build and display a generic modal dialog.
 * - `alert(title, message, type, extraOptions)` OR `alert(options)` — Show an alert with a single "Close" button.
 * - `confirm(title, message, extraOptions)` OR `confirm(options)` — Show a confirmation dialog with "OK" and "Cancel".
 * - `prompt(title, message, placeholder, defaultValue, extraOptions)` OR `prompt(options)` — Show a prompt with text input.
 * - `notify(title, message, type, extraOptions)` OR `notify(options)` — Show a notification dialog that auto-closes after a timeout.
 * - `loading(show)` — Show or hide a full-screen loading overlay.
 *
 * ## Usage Examples
 * ```js
 * // Simple alert (param style)
 * await Dialog.alert("Warning!", "Something went wrong", "danger");
 *
 * // Alert with options object
 * await Dialog.alert({
 *   title: "Warning!",
 *   message: "Something went wrong",
 *   type: "danger",
 *   size: "modal-lg",
 *   autoClose: 5000
 * });
 *
 * // Confirm with auto-close (param style + extraOptions)
 * const confirmed = await Dialog.confirm("Delete Confirmation", "Are you sure?", { autoClose: 5000 });
 *
 * // Prompt with default value (options style)
 * const name = await Dialog.prompt({
 *   title: "User Input",
 *   message: "Enter your name:",
 *   placeholder: "Your name here",
 *   defaultValue: "Anton"
 * });
 *
 * // Notify (auto-close notification)
 * await Dialog.notify("Data saved successfully", "Success", "success", { autoClose: 3000 });
 *
 * // Global defaults
 * Dialog.setDefaults({ size: 'modal-lg', backdrop: true });
 * ```
 *
 * @class Dialog
 * @exports Dialog
 */

class Dialog {

    /**
     * Default configuration values applied to all Dialog unless overridden.
     * @type {{backdrop:string, keyboard:boolean, size:string, centered:boolean}}
     */
    static defaults = {
        backdrop: 'static',
        keyboard: true,
        size: '',
        centered: true
    };

    /**
     * Predefined theme mappings for common dialog types.
     * Each theme defines a header CSS class and an icon class.
     * @type {Object<string,{headerClass:string,icon:string}>}
     */
    static themes = {
        success: {headerClass: 'border-success', icon: 'cil-check-circle text-success'},
        danger: {headerClass: 'border-danger', icon: 'cil-report-slash text-danger'},
        error: {headerClass: 'border-danger', icon: 'cil-report-slash text-danger'},
        warning: {headerClass: 'border-warning', icon: 'cil-warning text-warning'},
        info: {headerClass: 'border-info', icon: 'cil-info text-info'}
    };

    /**
     * Updates the global default configuration for all Dialog.
     * This allows developers to set common properties (such as size, backdrop, keyboard behavior, etc.)
     * once, and have them applied automatically to every dialog method (`alert`, `confirm`, `prompt`, etc.)
     * unless overridden by per-call options.
     *
     * @param {Object} newDefaults - Partial object of default configuration values to override.
     * @param {string|boolean} [newDefaults.backdrop] - Backdrop behavior (`'static'`, `true`, or `false`).
     * @param {boolean} [newDefaults.keyboard] - Whether ESC key closes the modal.
     * @param {string} [newDefaults.size] - Modal size class (`'modal-sm'`, `'modal-lg'`, `'modal-xl'`).
     * @param {boolean} [newDefaults.centered] - Whether to vertically center the modal.
     *
     * @example
     * // Set global defaults once
     * Dialog.setDefaults({
     *   size: 'modal-lg',
     *   backdrop: true,
     *   centered: false
     * });
     *
     * // All subsequent Dialog will use these defaults
     * await Dialog.alert("Warning!", "Something went wrong", "danger");
     */
    static setDefaults(newDefaults = {}) {
        this.defaults = {...this.defaults, ...newDefaults};
    }

    /**
     * Internal helper to build and display a generic modal dialog.
     * Dynamically creates a CoreUI modal with title, message, icon, and footer buttons.
     * Supports optional body content injection and auto-close countdown.
     *
     * @private
     * @param {Object} options - Configuration options for the modal.
     * @param {string} options.title - Title text displayed in the modal header.
     * @param {string} options.message - Message text displayed in the modal body.
     * @param {Array<{label:string,class:string,value:string}>} options.buttons - Array of button definitions.
     * @param {string} [options.icon='cil-warning'] - CSS class for the icon.
     * @param {boolean} [options.centered=true] - Whether to vertically center the modal.
     * @param {string} [options.size=''] - Modal size class ('modal-sm', 'modal-lg', 'modal-xl').
     * @param {string} [options.headerClass=''] - Extra CSS classes for the header.
     * @param {string|boolean} [options.backdrop='static'] - Backdrop behavior.
     * @param {boolean} [options.keyboard=true] - Whether ESC key closes the modal.
     * @param {string|null} [options.defaultResolveValue=null] - Value to resolve if dismissed without button click.
     * @param {string|null} [options.bodyContent=null] - Optional HTML string injected into the body.
     * @param {number|null} [options.autoClose=null] - Auto-close timeout in milliseconds.
     * @returns {Promise<string|null>} Resolves with the `data-value` of the clicked button or default value.
     */
    static _renderModal(options) {
        // noinspection JSUnresolvedReference
        if (typeof coreui === 'undefined' || !coreui.Modal) {
            throw new Error("CoreUI Modal dependency not found");
        }

        const {
            title,
            message,
            buttons,
            icon = 'cil-warning',
            centered = Dialog.defaults.centered,
            size = Dialog.defaults.size,
            headerClass = '',
            backdrop = Dialog.defaults.backdrop,
            keyboard = Dialog.defaults.keyboard,
            defaultResolveValue = null,
            bodyContent = null,
            autoClose = null
        } = options;

        const modalId = 'cui-modal-' + Date.now() + Math.floor(Math.random() * 1000);

        const modalEl = document.createElement('div');
        modalEl.className = 'modal fade';
        modalEl.id = modalId;
        modalEl.tabIndex = -1;
        modalEl.setAttribute('role', 'dialog');
        modalEl.setAttribute('aria-modal', 'true');
        modalEl.setAttribute('aria-labelledby', `${modalId}Label`);
        modalEl.setAttribute('aria-hidden', 'true');

        modalEl.innerHTML = `
            <div class="modal-dialog ${centered ? 'modal-dialog-centered' : ''} ${size}">
                <div class="modal-content border-0 rounded-0">
                    <div class="modal-header border-start-0 border-end-0 border-bottom-0 border-top border-4 rounded-0 ${headerClass}"></div>
                    <div class="modal-body text-center py-4 text-secondary">
                        <i class="${icon} icon icon-4xl"></i>
                        <h2 id="${modalId}Label" class="fw-bolder py-3 text-dark">${title}</h2>
                        <p>${message}</p>
                        ${bodyContent ? bodyContent : ''}
                    </div>
                    <div class="modal-footer bg-light">
                        ${buttons.map(btn => `
                            <button type="button" class="btn btn-sm fw-bolder px-5 rounded-pill ${btn.class}" data-value="${btn.value}">${btn.label}</button>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;

        document.body.append(modalEl);
        // noinspection JSUnresolvedReference
        const modalInstance = new coreui.Modal(modalEl, {backdrop, keyboard});

        return new Promise((resolve) => {
            let isResolved = false;

            const resolveAndHide = (value) => {
                if (isResolved) return;
                isResolved = true;
                resolve(value);
                if (document.activeElement instanceof HTMLElement) {
                    document.activeElement.blur();
                }
                modalInstance.hide();
            };

            modalEl.querySelector('.modal-footer').addEventListener('click', (e) => {
                const button = e.target.closest('button');
                if (button && button.hasAttribute('data-value')) {
                    resolveAndHide(button.getAttribute('data-value'));
                }
            }, {once: true});

            modalEl.addEventListener('hidden.coreui.modal', () => {
                if (!isResolved) resolveAndHide(defaultResolveValue);
                modalEl.remove();
            });

            modalInstance.show();

            // Auto-close logic
            if (autoClose) {
                let remaining = autoClose / 1000;
                const footer = modalEl.querySelector('.modal-footer');
                const countdownEl = document.createElement('span');
                countdownEl.className = 'me-auto text-muted small';
                countdownEl.textContent = `Closing in ${remaining} seconds`;
                footer.prepend(countdownEl);

                const interval = setInterval(() => {
                    remaining--;
                    countdownEl.textContent = `Closing in ${remaining} seconds`;
                    if (remaining <= 0) {
                        clearInterval(interval);
                        resolveAndHide(defaultResolveValue);
                    }
                }, 1000);
            }
        });
    }

    /**
     * Displays an alert modal with a single "Close" button.
     *
     * This method is used to show important information or warnings that require
     * user acknowledgement. The dialog contains a message and a single "Close" button.
     * It resolves once the user closes the dialog.
     *
     * ## Hybrid API
     * - **Simple parameters style:**
     *   `alert(title, message, type, extraOptions)`
     * - **Options object style:**
     *   `alert(options)`
     *
     * @param {string|Object} titleOrOptions - Title string OR full options object.
     *   - If a string is provided, it is used as the dialog title.
     *   - If an object is provided, it may contain all configuration properties.
     * @param {string} [message] - The message text to display (ignored if options object is passed).
     * @param {'success'|'danger'|'error'|'warning'|'info'} [type='warning'] - Theme type for styling (ignored if options object is passed).
     * @param {Object} [extraOptions={}] - Additional options to merge (only for param style).
     *   - `headerClass` {string} — Custom CSS class for the header.
     *   - `icon` {string} — Custom icon class.
     *   - `size` {string} — Modal size (`'modal-sm'`, `'modal-lg'`, `'modal-xl'`).
     *   - `backdrop` {string|boolean} — Backdrop behavior.
     *   - `keyboard` {boolean} — Whether ESC key closes the modal.
     *   - `autoClose` {number} — Auto-close timeout in milliseconds.
     *   - `countdownPosition` {'left'|'right'} — Position of the countdown indicator in the footer.
     *
     * @returns {Promise<void>} Resolves when the modal is closed.
     *
     * @example
     * // Simple usage (param style)
     * await Dialog.alert("Form submission failed", "Error", "danger");
     *
     * // Extended usage with extra options
     * await Dialog.alert("Session Expired", "You will be logged out automatically", "warning", {
     *   autoClose: 5000,
     *   countdownPosition: "left"
     * });
     *
     * // Options object style
     * await Dialog.alert({
     *   title: "Information",
     *   message: "Your settings have been updated",
     *   type: "info",
     *   size: "modal-sm",
     *   autoClose: 3000,
     *   countdownPosition: "right"
     * });
     */
    static async alert(titleOrOptions, message, type = 'warning', extraOptions = {}) {
        let options;
        if (typeof titleOrOptions === 'object') {
            const theme = Dialog.themes[titleOrOptions.type] || Dialog.themes.info;
            options = {
                buttons: [{label: 'Close', class: 'btn-light', value: 'close'}],
                headerClass: theme.headerClass,
                icon: theme.icon,
                defaultResolveValue: 'close',
                ...titleOrOptions
            };
        } else {
            const theme = Dialog.themes[type] || Dialog.themes.info;
            options = {
                title: titleOrOptions,
                message,
                buttons: [{label: 'Close', class: 'btn-light', value: 'close'}],
                headerClass: theme.headerClass,
                icon: theme.icon,
                defaultResolveValue: 'close',
                ...extraOptions
            };
        }
        await this._renderModal(options);
    }

    /**
     * Displays a confirmation modal with "OK" and "Cancel" buttons.
     *
     * This method is used when you need the user to explicitly choose between
     * a positive action (OK) and a negative action (Cancel).
     * It returns a boolean value based on the user's choice.
     *
     * ## Hybrid API
     * - **Simple parameters style:**
     *   `confirm(title, message, extraOptions)`
     * - **Options object style:**
     *   `confirm(options)`
     *
     * @param {string|Object} titleOrOptions - Title string OR full options object.
     *   - If a string is provided, it is used as the dialog title.
     *   - If an object is provided, it may contain all configuration properties.
     * @param {string} [message] - The message text to display (ignored if options object is passed).
     * @param {Object} [extraOptions={}] - Additional options to merge (only for param style).
     *   - `headerClass` {string} — Custom CSS class for the header.
     *   - `icon` {string} — Custom icon class.
     *   - `size` {string} — Modal size (`'modal-sm'`, `'modal-lg'`, `'modal-xl'`).
     *   - `backdrop` {string|boolean} — Backdrop behavior.
     *   - `keyboard` {boolean} — Whether ESC key closes the modal (default: `false` for confirm).
     *   - `autoClose` {number} — Auto-close timeout in milliseconds.
     *   - `countdownPosition` {'left'|'right'} — Position of the countdown indicator in the footer.
     *
     * @returns {Promise<boolean>} Resolves to `true` if "OK" is clicked,
     * otherwise `false` if "Cancel" is clicked or the dialog is dismissed.
     *
     * @example
     * // Simple usage (param style)
     * const confirmed = await Dialog.confirm("Delete Confirmation", "Are you sure?");
     * if (confirmed) {
     *   console.log("User confirmed deletion");
     * }
     *
     * // Extended usage with extra options
     * const confirmed = await Dialog.confirm("Session Timeout", "Do you want to stay logged in?", {
     *   autoClose: 5000,
     *   countdownPosition: "left",
     *   size: "modal-sm"
     * });
     *
     * // Options object style
     * const confirmed = await Dialog.confirm({
     *   title: "Exit Application",
     *   message: "Are you sure you want to quit?",
     *   headerClass: "border-danger",
     *   icon: "cil-exit text-danger",
     *   autoClose: 4000,
     *   countdownPosition: "right"
     * });
     */
    static async confirm(titleOrOptions, message, extraOptions = {}) {
        let options;
        if (typeof titleOrOptions === 'object') {
            options = {
                buttons: [
                    {label: 'Cancel', class: 'btn-light', value: 'false'},
                    {label: 'OK', class: 'btn-warning text-white', value: 'true'}
                ],
                headerClass: Dialog.themes.warning.headerClass,
                icon: Dialog.themes.warning.icon,
                keyboard: false,
                defaultResolveValue: 'false',
                ...titleOrOptions
            };
        } else {
            options = {
                title: titleOrOptions,
                message,
                buttons: [
                    {label: 'Cancel', class: 'btn-light', value: 'false'},
                    {label: 'OK', class: 'btn-warning text-white', value: 'true'}
                ],
                headerClass: Dialog.themes.warning.headerClass,
                icon: Dialog.themes.warning.icon,
                keyboard: false,
                defaultResolveValue: 'false',
                ...extraOptions
            };
        }
        const result = await this._renderModal(options);
        return result === 'true';
    }

    /**
     * Displays a prompt dialog with a text input field.
     *
     * This method is used when you need to collect a single string value from the user.
     * The dialog contains a text input field and "OK"/"Cancel" buttons.
     * If the user clicks "OK", the entered value is returned; if canceled or dismissed, `null` is returned.
     *
     * ## Hybrid API
     * - **Simple parameters style:**
     *   `prompt(title, message, placeholder, defaultValue, extraOptions)`
     * - **Options object style:**
     *   `prompt(options)`
     *
     * @param {string|Object} titleOrOptions - Title string OR full options object.
     *   - If a string is provided, it is used as the dialog title.
     *   - If an object is provided, it may contain all configuration properties.
     * @param {string} [message] - Instruction or message displayed above the input (ignored if options object is passed).
     * @param {string} [placeholder=''] - Placeholder text for the input field (ignored if options object is passed).
     * @param {string} [defaultValue=''] - Default value pre-filled in the input field (ignored if options object is passed).
     * @param {Object} [extraOptions={}] - Additional options to merge (only for param style).
     *   - `headerClass` {string} — Custom CSS class for the header.
     *   - `icon` {string} — Custom icon class.
     *   - `size` {string} — Modal size (`'modal-sm'`, `'modal-lg'`, `'modal-xl'`).
     *   - `backdrop` {string|boolean} — Backdrop behavior.
     *   - `keyboard` {boolean} — Whether ESC key closes the modal.
     *
     * @returns {Promise<string|null>} Resolves with the entered value if "OK" is clicked,
     * or `null` if canceled/dismissed.
     *
     * @example
     * // Simple usage (param style)
     * const name = await Dialog.prompt("User Input", "Enter your name:", "Your name here");
     * if (name) console.log("User entered:", name);
     *
     * // Extended usage with extra options
     * const email = await Dialog.prompt("Email Input", "Enter your email:", "example@mail.com", "", {
     *   size: "modal-lg",
     *   icon: "cil-envelope text-primary"
     * });
     *
     * // Options object style
     * const username = await Dialog.prompt({
     *   title: "Username",
     *   message: "Enter your username:",
     *   placeholder: "Your username here",
     *   defaultValue: "Anton",
     *   size: "modal-sm",
     *   icon: "cil-user text-info"
     * });
     */
    static async prompt(titleOrOptions, message, placeholder = '', defaultValue = '', extraOptions = {}) {
        let options;
        let inputId = 'input-' + Date.now();

        if (typeof titleOrOptions === 'object') {
            const bodyContent = `
            <input type="text" class="form-control mt-3" id="${inputId}"
                   placeholder="${titleOrOptions.placeholder || ''}" value="${titleOrOptions.defaultValue || ''}"
                   aria-describedby="${inputId}-desc">
            <small id="${inputId}-desc" class="form-text text-muted">${titleOrOptions.message}</small>
        `;
            options = {
                buttons: [
                    {label: 'Cancel', class: 'btn-light', value: 'cancel'},
                    {label: 'OK', class: 'btn-primary text-white', value: 'ok'}
                ],
                headerClass: Dialog.themes.info.headerClass,
                icon: 'cil-pencil text-info',
                keyboard: false,
                defaultResolveValue: null,
                bodyContent,
                ...titleOrOptions
            };
        } else {
            const bodyContent = `
            <input type="text" class="form-control mt-3" id="${inputId}"
                   placeholder="${placeholder}" value="${defaultValue}"
                   aria-describedby="${inputId}-desc">
            <small id="${inputId}-desc" class="form-text text-muted">${message}</small>
        `;
            options = {
                title: titleOrOptions,
                message,
                buttons: [
                    {label: 'Cancel', class: 'btn-light', value: 'cancel'},
                    {label: 'OK', class: 'btn-primary text-white', value: 'ok'}
                ],
                headerClass: Dialog.themes.info.headerClass,
                icon: 'cil-pencil text-info',
                keyboard: false,
                defaultResolveValue: null,
                bodyContent,
                ...extraOptions
            };
        }

        const result = await this._renderModal(options);
        if (result === 'ok') {
            return document.getElementById(inputId)?.value || null;
        }
        return null;
    }

    /**
     * Displays a notification dialog that auto-closes after a timeout.
     *
     * This method is intended for non-blocking informational messages.
     * Unlike `alert`, it does not require user interaction (no buttons by default)
     * and will automatically close after the specified duration.
     *
     * ## Hybrid API
     * - **Simple parameters style:**
     *   `notify(title, message, type, extraOptions)`
     * - **Options object style:**
     *   `notify(options)`
     *
     * @param {string|Object} titleOrOptions - Title string OR full options object.
     *   - If a string is provided, it is used as the dialog title.
     *   - If an object is provided, it may contain all configuration properties.
     * @param {string} [message] - Message text to display (ignored if options object is passed).
     * @param {'success'|'danger'|'error'|'warning'|'info'} [type='info'] - Theme type for styling (ignored if options object is passed).
     * @param {Object} [extraOptions={}] - Additional options to merge (only for param style).
     * @param {number} [extraOptions.autoClose=3000] - Auto-close timeout in milliseconds.
     * @param {'left'|'right'} [extraOptions.countdownPosition='left'] - Position of the countdown indicator in the footer.
     *
     * @returns {Promise<void>} Resolves when the notification closes automatically.
     *
     * @example
     * // Simple usage (param style)
     * await Dialog.notify("Data Saved", "Your changes have been saved successfully", "success");
     *
     * // Extended usage with extra options
     * await Dialog.notify("Auto-close Demo", "This will close in 5 seconds", "info", {
     *   autoClose: 5000,
     *   countdownPosition: 'left'
     * });
     *
     * // Options object style
     * await Dialog.notify({
     *   title: "Success",
     *   message: "Data saved successfully",
     *   type: "success",
     *   autoClose: 4000,
     *   countdownPosition: "right"
     * });
     */
    static async notify(titleOrOptions, message, type = 'info', extraOptions = {}) {
        let options;
        if (typeof titleOrOptions === 'object') {
            const theme = Dialog.themes[titleOrOptions.type] || Dialog.themes.info;
            options = {
                buttons: [],
                headerClass: theme.headerClass,
                icon: theme.icon,
                defaultResolveValue: null,
                autoClose: titleOrOptions.autoClose || 3000,
                countdownPosition: titleOrOptions.countdownPosition || 'left',
                ...titleOrOptions
            };
        } else {
            const theme = Dialog.themes[type] || Dialog.themes.info;
            options = {
                title: titleOrOptions,
                message,
                buttons: [],
                headerClass: theme.headerClass,
                icon: theme.icon,
                defaultResolveValue: null,
                autoClose: 3000,
                countdownPosition: 'left',
                ...extraOptions
            };
        }
        await this._renderModal(options);
    }
}

// export default Dialog;