<?php

spl_autoload_register(function ($class_name) {
    require_once $class_name.'.php';
 });

 //** CONFIGURARION TABLE MAPPER *************************/
$DB_NAME = "foronum";
$DB_TABLE_NAME = "t_subcategoria";
$DB_PK = "id";
$CLASS_NAME = "ADOTiendaSubCategoria";

 ///** CONFIGURATION DB ***********************************/
$db  = DatabaseMySQL::Connect();

// Query to get columns from table
$query = $db->Query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$DB_NAME' AND TABLE_NAME = '$DB_TABLE_NAME'");

while($row = $query->fetch_assoc()){
    $result[] = $row;
}

// Array of all column names
$columnArr = array_column($result, 'COLUMN_NAME');

//** PRINT CLASS ******************************************/

echo "<pre>";

    echo htmlentities("<?php");
    echo "\n\nclass $CLASS_NAME extends ADOBase{\n\n";

    echo "// atributos\n";
    foreach ($columnArr as $key => $value) {
        echo "public $".$value.";\n";
    }

    echo "\n// table config\n";
    echo "protected \$class_name = \"$CLASS_NAME\";\n";
    echo "protected \$table_name = \"$DB_TABLE_NAME\";\n";
    echo "protected \$fields = array(\"".implode('","', $columnArr)."\");\n";
    echo "protected \$pks = array(\"$DB_PK\");\n";

    echo "\n\nfunction __construct(";
    foreach ($columnArr as $key => $value) {
        if($key != 0) echo ",";
        echo "$".$value."=''";
    }
    echo ")\n";

    echo "{\n";
        foreach ($columnArr as $key => $value) {
            echo "if (!\$this->$value) {\$this->$value = \$$value;}\n";
        }
    echo "}\n\n";

    echo "//  additional methods\n";

    echo "}\n";


    echo "\n?>";

echo "</pre>";
?>