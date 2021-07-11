# Rabble User Bundle
The Rabble user bundle adds user and permission management to Rabble, as well as a way to login to the admin panel.
It is generally a good idea to install this bundle when using Rabble, unless you want to create your own user bundle.

# Installation
Install the bundle by running
```sh
composer require rabble/user-bundle
```

Add the following class to your `config/bundles.php` file:
```php
return [
    ...
    Rabble\UserBundle\RabbleUserBundle::class => ['all' => true],
]
```
