# HTTP Auth Wrapper
Library provides simple HTTP authentication

# Installation
Just download and include classes from `src` or
use Composer:

`composer require mikechip/php-httpauth`

# Sample use
```php
    require_once('vendor/autoload.php');
    
    $auth = new Mike4ip\HttpAuth();
    $auth->addLogin('admin', 'test');
    $auth->addLogin('foo', 'bar');
    $auth->requireAuth();
    
    print('This is your hidden page');
```

# Customization
```php
    require_once('vendor/autoload.php');

    /*
     * HTTP Auth with customization
     */
    $auth = new Mike4ip\HttpAuth();
    $auth->setRealm('Pass login and password');
    
    // Set unauthorized callback
    $auth->onUnauthorized(function() {
        print("<h1>403 Forbidden</h1>");
        die;
    })->setCheckFunction(function($user, $pwd) {
        // List of logins => passwords
        $users = [
        'admin' => 'test',
        'foo' => 'bar'
        ];
    
        // Returns true if login and password matches
        return (isset($users[$user]) && $users[$user] === $pwd);
    })->requireAuth();

    print('This is your hidden page');
```

# Feedback
Use **Issues** to contact me