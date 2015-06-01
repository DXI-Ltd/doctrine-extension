# DXI Doctrine Extension

Doctrine 2 extensions

## Installation

Add the **dxi/doctrine-extension** into **composer.json**

```json
{
    "require": {
        "php":              ">=5.3.2",
        "dxi/doctrine-extension": "~1.0"
    }
}
```

## Enum Extension

This Extension provides convenient way to create Doctrine Mapping Types from your Enum Types.
For now, only ***marc-mabe/php-enum*** is supported (see https://github.com/marc-mabe/php-enum).

*  Create your Enum Type
 
```php
class MyEnum extends \MabeEnum\Enum
{
    const ONE = 'one';
    const TWO = 'two';
}
```

* Define your Entity or Document
```php
class MyEntity
{
    private $id;
    
    /**
     * @var MyEnum
     */
    private $myEnum;
    
    /**
     * @return MyEnum
     */
    public function getMyEnum()
    {
        return $this->myEnum;
    }
    
    /**
     * @param MyEnum $myEnum
     */
    public function setMyEnum(MyEnum $myEnum)
    {
        $this->myEnum = $myEnum;
    }
    
    public function getId()
    {
        return $this->id;
    }
}
```

*  Register your type with DBAL
```php
use Dxi\DoctrineExtension\Enum\DBAL\DBALTypeRegistrar;
use Dxi\DoctrineExtension\Enum\DBAL\DBALTypeClassGenerator;

$targetDir = './enum-db-types';
$typeNamespace = 'My\Enum\DBALTypes';

$registrar = new DBALTypeRegistrar(new DBALTypeClassGenerator($targetDir, $typeNamespace));
$registrar->registerType('my_enum_type', '\MyEnum');
```
Now you can use ***my_enum_type*** with for Doctrine ORM mappings and with DBAL bindings.
(see [http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/cookbook/custom-mapping-types.html "DoctrineORM Custom Mapping Type"))
Notice: Field mapped as ***my_enum_type*** creates column type "VARCHAR" (32 length by default, however you can use other "string" type properties)

*  Register your type with MongoDB
```php
use Dxi\DoctrineExtension\Enum\DBAL\DBALTypeRegistrar;
use Dxi\DoctrineExtension\Enum\DBAL\DBALTypeClassGenerator;

$targetDir = './enum-mongo-types';
$typeNamespace = 'My\Enum\MongoDbTypes';

$registrar = new MongoDbTypeRegistrar(new MongoDbTypeClassRegistrar($targetDir, $typeNamespace));
$registrar->registerType('my_enum_type', '\MyEnum');
```
Now you can use ***my_enum_type*** for your Doctrine MongoDB ODM mappings
(see [http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/basic-mapping.html#custom-mapping-types](http://docs.doctrine-project.org/projects/doctrine-mongodb-odm/en/latest/reference/basic-mapping.html#custom-mapping-types "DoctrineMongoDbODM Custom Mapping Type"))

## Reference Extension

It's a fork of the Gedmo/References extension. See [https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/references.md](https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/references.md "Gedmo/References extension")
It provides multi registry support and XML/YML mapping drivers.
Note, this Extension will be removed, when it become part of Gedmo Doctrine Extension project (see [https://github.com/Atlantic18/DoctrineExtensions/pull/1320](https://github.com/Atlantic18/DoctrineExtensions/pull/1320))
