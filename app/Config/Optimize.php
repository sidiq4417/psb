<?php

namespace Config;

/**
 * Optimization Configuration.
 *
 * NOTE: This class does not extend BaseConfig for performance reasons.
 *       So you cannot replace the property values with Environment Variables.
 *
 * WARNING: Do not use these options when running the app in the Worker Mode.
 */
class Optimize
{
    /**
     * --------------------------------------------------------------------------
     * Config Caching
     * --------------------------------------------------------------------------
     *
     * Disabled: the cache keeps Config objects (including .env values such as
     * app.baseURL and database credentials) until writable/cache is cleared, so
     * editing .env on the hosting panel would silently have no effect.
     *
     * @see https://codeigniter.com/user_guide/concepts/factories.html#config-caching
     */
    public bool $configCacheEnabled = false;

    /**
     * --------------------------------------------------------------------------
     * Config Caching
     * --------------------------------------------------------------------------
     *
     * Disabled: the cache stores absolute file paths, so a cache file created on
     * another machine or folder (e.g. copied inside writable/) breaks every request.
     *
     * @see https://codeigniter.com/user_guide/concepts/autoloader.html#file-locator-caching
     */
    public bool $locatorCacheEnabled = false;
}
