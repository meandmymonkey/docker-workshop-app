<?php

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\UrlGeneratorServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();

$app->register(new UrlGeneratorServiceProvider());

$app->register(
    new TwigServiceProvider(),
    array(
        'twig.path' => __DIR__ . '/../resources/views',
        'twig.options' => array(
            'debug'=> true,
            'cache' => __DIR__ . '/../cache'
        )
    )
);

$app['db'] = $app->share(
    function() use ($app) {
        return new \PDO(
            'mysql:host=localhost;dbname=dockerworkshop',
            'root',
            null,
            array(
                \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            )
        );
    }
);

$app['log'] = $app->share(
    function() use ($app) {
        $connection = $app['db'];
        
        return function(Request $request) use ($connection) {
            $ip = $request->getClientIp();
            
            $q = $connection->prepare(
                'INSERT INTO visits (ip, time) VALUES (:ip, NOW())'
            );
            $q->bindParam(':ip', $ip);
            $q->execute();
        };
    }
);

$app['counter'] = $app->share(
    function() use ($app) {
        $connection = $app['db'];
        
        return function() use ($connection) {
            $q = $connection->query(
                'SELECT count(*) FROM visits'
            );
            
            return $q->fetchColumn();
        };
    }
);

$app['visit'] = $app->share(
    function() use ($app) {
        $connection = $app['db'];
        
        return function() use ($connection) {
            $q = $connection->query(
                'SELECT * FROM visits ORDER BY time DESC LIMIT 1'
            );
            
            return $q->fetch(\PDO::FETCH_ASSOC);
        };
    }
);

$app->get(
    '/',
    function(Request $request) use ($app) {
        $app['log']($request);
        
        return new Response(
            $app['twig']->render(
                'index.html.twig',
                array(
                    'counter' => $app['counter'](),
                    'visit' => $app['visit']()
                )
            )
        );
    }
)->bind('home');

return $app;