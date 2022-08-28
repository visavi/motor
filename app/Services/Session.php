<?php

declare(strict_types=1);

namespace App\Services;

/**
 * Session class
 */
class Session
{
    /**
     * Get a session variable.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        $session = $_SESSION;

        if (! str_contains($key, '.')) {
            return $session[$key] ?? $default;
        }

        foreach (explode('.', $key) as $segment) {
            if (is_array($session) && array_key_exists($segment, $session)) {
                $session = $session[$segment];
            } else {
                return $default;
            }
        }

        return $session;
    }

    /**
     * Set a session variable.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function set(string $key, mixed $value): static
    {
        $_SESSION[$key] = $value;

        return $this;
    }

    /**
     * Merge values recursively.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return $this
     */
    public function merge(string $key, mixed $value): static
    {
        if (is_array($value) && is_array($old = $this->get($key))) {
            $value = array_merge_recursive($old, $value);
        }

        return $this->set($key, $value);
    }

    /**
     * Delete a session variable.
     *
     * @param string $key
     *
     * @return $this
     */
    public function delete(string $key): static
    {
        if ($this->has($key)) {
            unset($_SESSION[$key]);
        }

        return $this;
    }

    /**
     * Clear all session variables.
     *
     * @return $this
     */
    public function clear(): static
    {
        $_SESSION = [];

        return $this;
    }

    /**
     * Check if a session variable is set.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $_SESSION);
    }

    /**
     * Get or regenerate current session ID.
     *
     * @param bool $new
     *
     * @return string
     */
    public function id(bool $new = false): string
    {
        if ($new && session_id()) {
            session_regenerate_id(true);
        }

        return session_id() ?: '';
    }

    /**
     * Destroy the session.
     */
    public function destroy(): void
    {
        if ($this->id()) {
            session_unset();
            session_destroy();
            session_write_close();

            if (ini_get('session.use_cookies')) {
                $options = [
                    'expires' => strtotime('-1 hour'),
                    'path' => '/',
                    'domain'   => setting('session.cookie_domain'),
                    'secure'   => setting('session.cookie_secure'),
                    'httponly' => setting('session.cookie_httponly'),
                    'samesite' => setting('session.cookie_samesite'),
                ];

                setcookie(session_name(), '', $options);
            }
        }
    }
}
