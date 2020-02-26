<?php
// bootstrap.php
use SimpleSAML\SimpleSAMLphpNamingStrategy;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\EntityManager;
use SimpleSAML\Configuration;

require_once 'lib/_autoload.php';

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = true;
$proxyDir = null;
$cache = null;
$useSimpleAnnotationReader = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . '/src'), $isDevMode, $proxyDir, $cache, $useSimpleAnnotationReader);
//TODO: Fix naming strategy
$namingStrategy = new SimpleSAMLphpNamingStrategy();
$config->setNamingStrategy($namingStrategy);

$simpleSAMLconfig = Configuration::getInstance();
$databaseConfig = $simpleSAMLconfig->getArray('store.sql.dsn');

// database configuration parameters
$conn = [];

switch ($databaseConfig['type']) {
    case 'mysql':
        $conn['driver'] = 'pdo_mysql';
        foreach (['host', 'port', 'dbname', 'unix_socket', 'charset'] as $property) {
            if (array_key_exists($property, $databaseConfig['properties'])) {
                $conn[$property] = $databaseConfig['properties'][$property];
            }
        }
        break;
    case 'sqlsrv':
        $conn['driver'] = 'pdo_sqlsrv';
        foreach (['host', 'port', 'dbname'] as $property) {
            if (array_key_exists($property, $databaseConfig['properties'])) {
                $conn[$property] = $databaseConfig['properties'][$property];
            }
        }
        break;
    case 'pdo_pgsql':
        $conn['driver'] = 'pdo_pgsql';
        foreach (['host', 'port', 'dbname', 'charset', 'sslmode', 'sslrootcert', 'sslcert', 'sslkey', 'sslcrl', 'application_name'] as $property) {
            if (array_key_exists($property, $databaseConfig['properties'])) {
                $conn[$property] = $databaseConfig['properties'][$property];
            }
        }
        break;
    case 'pdo_oci':
        $conn['driver'] = 'pdo_oci';
        foreach (['host', 'port', 'dbname', 'servicename', 'service', 'pooled', 'charset', 'instancename', 'connectstring', 'persistent'] as $property) {
            if (array_key_exists($property, $databaseConfig['properties'])) {
                $conn[$property] = $databaseConfig['properties'][$property];
            }
        }
        break;
    case 'sqlite':
        $conn['driver'] = 'pdo_sqlite';
        foreach (['path'] as $property) {
            if (array_key_exists($property, $databaseConfig['properties'])) {
                $conn[$property] = $databaseConfig['properties'][$property];
            }
        }
        $conn['path'] = __DIR__ . '/db.sqlite';
        break;
}
$conn['user'] = $simpleSAMLconfig->getString('store.sql.username', 'root');
$conn['password'] = $simpleSAMLconfig->getString('store.sql.password', 'root');

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);
