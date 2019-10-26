<?php

namespace Mike4ip;

use Closure;

/**
 * Class HttpAuth
 * @package Mike4ip
 */
class HttpAuth
{
    /**
     * Text string shown while autorization
     * @var string
     */
    protected $realm;

    /**
     * Called if user passed bad credentials
     * @var Closure
     */
    protected $on_unauthorized;

    /**
     * Called to check login and password. Must return true or false (successful/failed authorization)
     * @var Closure
     */
    protected $check_function;

    /**
     * Logins and passwords allowed
     * @var array
     */
    protected $credentials = [];

    /**
     * HttpAuth constructor.
     */
    public function __construct()
    {
        $this->on_unauthorized = function() {
            throw new HttpAuthException('Unauthorized');
        };
        $this->realm = 'Authorization required';
    }

    /**
     * @param string $login
     * @param string $password
     * @return HttpAuth
     */
    public function addLogin(string $login, string $password): self
    {
        $this->credentials[$login] = $password;
        return $this;
    }

    /**
     * Set text string shown while autorization
     * @param string $text
     * @return HttpAuth
     */
    public function setRealm(string $text): self
    {
        $this->realm = $text;
        return $this;
    }

    /**
     * Set function to check login and password.
     * Must return true or false (successful/failed authorization)
     * @param callable $callback
     * @return HttpAuth
     */
    public function setCheckFunction(callable $callback): self
    {
        $this->check_function = $callback;
        return $this;
    }

    /**
     * Called if user passed bad credentials
     * @param callable $callback
     * @return HttpAuth
     */
    public function onUnauthorized(callable $callback): self
    {
        $this->on_unauthorized = $callback;
        return $this;
    }

    /**
     * Show authorization headers
     */
    protected function showHeaders(): void
    {
        header('WWW-Authenticate: Basic realm="'.$this->realm.'"');
        header('HTTP/1.0 401 Unauthorized');
    }

    public function requireAuth(array $server = null): bool
    {
        if(!$server)
            $server = $_SERVER;

        if(!isset($server['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])) {
            $this->showHeaders();
            call_user_func($this->on_unauthorized);
            return false;
        } else {
            $user = $server['PHP_AUTH_USER'];
            $pwd = $server['PHP_AUTH_PW'];

            if(is_callable($this->check_function)) {
                $result = call_user_func_array($this->check_function, [$user, $pwd]);
            } elseif(isset($this->credentials[$user]) && $this->credentials[$user] === $pwd)
                return true;
            else {
                call_user_func($this->on_unauthorized);
                return false;
            }

            if($result !== true) {
                call_user_func($this->on_unauthorized);
                return false;
            }
        }

        return true;
    }
}