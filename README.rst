Facebook Graph API Consumer in PHP
==================================

.. image:: https://travis-ci.org/nixilla/facebook-api-consumer.png?branch=master

Facebook Graph API Consumer in namespaced, easy to test PHP, continuously integrated with Travis CI.

Installation
------------

The easiest way - via packagist_ and composer_:

.. _packagist: https://packagist.org/packages/nixilla/facebook-api-consumer
.. _composer: http://getcomposer.org/

.. code-block:: json

    {
        "require": {
            "nixilla/facebook-api-consumer": "~0.1"
        }
    }

What does it do?
----------------

This library is a wrapper around Facebook Graph API
plus it provides some utils method often used in Facebook integrated applications.

How does it do it?
------------------

This library provides `Facebook\\Consumer` class which is a higher level wrapper around `kriswallsmith/buzz`_ library.
You just specify what end point you're interested in and it'll pull the information for you.

.. _`kriswallsmith/buzz`: https://packagist.org/packages/kriswallsmith/buzz

For example:

.. code-block:: php

    <?php

    require_once './vendor/autoload.php';

    $client = new Buzz\Browser(new Buzz\Client\Curl());
    $consumer = new Facebook\Consumer($client);

    $consumer->setAccessToken('ACCESS_TOKEN_STRING');

    $result = $consumer->call('/me/feed');


By default the `Facebook\\Consumer` converts the json output from Facebook API to PHP array. It does it using `Facebook\\DefaultConverter` class.

However you can change this behaviour by injecting your custom converter.
All you need to do is to create you converter class that implements `Facebook\\Converter` interface
and inject into `Facebook\\Consumer`.

For example if you are using Symfony 2 and have `AcmeDemoBundle:User` class and you want `Facebook\\Consumer` to output that class, you need to:

.. code-block:: php

    <?php

    # src/Acme/DemoBundle/Converter/UserConverter.php
    namespace Acme\DemoBundle\Converter;

    use Facebook\Converter;
    use Acme\DemoBundle\Entity\User;

    class UserConverter implements Converter
    {
        public function convert($json_string)
        {
            // do whatever you need to do, for example
            $user = new User()
            $user->fromArray(json_decode($json_string, true));

            return $user;
        }
    }

.. code-block:: php

    <?php

    # src/Acme/DemoBundle/Controller/UserController.php

    namespace Acme\DemoBundle\Converter;

    use Acme\DemoBundle\Converter\UserConverter;

    class UserController extends Controller
    {
        public function newAction(Request $request)
        {
            $consumer = $this->container->get('facebook.consumer');
            $consumer->setAccessToken($this->container->get('security.context')->getToken()->getAccessToken());
            $consumer->setConverter('/me', new UserConverter());

            $user = $consumer->call('/me');

            // you can now use it for forms
            $form = $this->createForm(new UserType(), $user);
        }
    }


@TODO - test if this is actually working
