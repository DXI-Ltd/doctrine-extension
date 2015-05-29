# DXI Doctrine ENUM

## Installation

Add the **dxi/doctrine-enum** into **composer.json**

```json
{
    "require": {
        "php":              ">=5.3.2",
        "dxi/doctrine-enum": "~1.0"
    }
}
```

## Usage

*  Create your Enum Type
 
```php
class MyEnum extends \MabeEnum\Enum
{
    const ONE = 'one';
    const TWO = 'two';
}
```

*  Register your type with DBAL
```php
use Dxi\DoctrineEnum\DBAL\DBALTypeRegistrar;
use Dxi\DoctrineEnum\DBAL\DBALTypeClassGenerator;

$targetDir = './enum-db-types';
$typeNamespace = 'My\Enum\DBALTypes';

$registrar = new DBALTypeRegistrar(new DBALTypeClassGenerator($targetDir, $typeNamespace));
$registrar->registerType('my_enum_type', '\MyEnum');
```
Now you can use "my_enum_type" with for Doctrine ORM mappings and with DBAL bindings.

*  Register your type with MongoDB
```php
use Dxi\DoctrineEnum\DBAL\DBALTypeRegistrar;
use Dxi\DoctrineEnum\DBAL\DBALTypeClassGenerator;

$targetDir = './enum-mongo-types';
$typeNamespace = 'My\Enum\MongoDbTypes';

$registrar = new MongoDbTypeRegistrar(new MongoDbTypeClassRegistrar($targetDir, $typeNamespace));
$registrar->registerType('my_enum_type', '\MyEnum');
```
Now you can use "my_enum_type" for your Doctrine MongoDB ODM mappings.
