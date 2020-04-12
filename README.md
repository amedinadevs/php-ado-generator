# How create ADO Class

## 1. Check config in AutoMapper.php

Puts your database name, table, primary key and the class name of your ADO.

```php
 //** CONFIGURARION TABLE MAPPER *************************/
$DB_NAME = "my_db";
$DB_TABLE_NAME = "my_table";
$DB_PK = "id";
$CLASS_NAME = "ADOMy_TABLE";
```

## 2. Run Apache and go to Automapper.php and see the class

Next steps is only run Apache and go to Automapper.php. It generates text with your class. You already can copy and paste  the new class in editor.