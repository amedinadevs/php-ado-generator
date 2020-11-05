<?php

spl_autoload_register(function ($class_name) {
    require_once $class_name.'.php';
 });

 //** CONFIGURARION TABLE MAPPER *************************/
$DB_NAME = "foronum";
$DB_TABLE_NAME = "intercambio_monedas";
$DB_PK = "intercambio_id\", \"intercambio_col_id, \"clave"; // COMPUESTAS -> "page\", \"language\", \"code";
//$DB_PK = "id";
$CLASS_NAME = "ADOIntercambioMonedas";

 ///** CONFIGURATION DB ***********************************/
$db  = DatabaseMySQL::Connect();

// Query to get columns from table
$query = $db->Query("SELECT COLUMN_NAME, COLUMN_DEFAULT, IS_NULLABLE, DATA_TYPE, COLUMN_KEY FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = '$DB_NAME' AND TABLE_NAME = '$DB_TABLE_NAME'");

while($row = $query->fetch_assoc()){
    $result[] = $row;
}

// Array of all column names
$array_names = array_column($result, 'COLUMN_NAME');

//** PRINT CLASS ******************************************/

echo "<pre>";

    echo htmlentities("<?php");
    echo "\n\nclass $CLASS_NAME extends ADOBase{\n\n";

    echo "// atributos\n";
    foreach ($result as $key => $value) {
        echo "public $".$value['COLUMN_NAME'].";   // ".$value['DATA_TYPE']."\n";
    }

    echo "\n// table config\n";
    echo "protected \$class_name = \"$CLASS_NAME\";\n";
    echo "protected \$table_name = \"$DB_TABLE_NAME\";\n";
    echo "protected \$fields = array(\"".implode('","', $array_names)."\");\n";
    echo "protected \$pks = array(\"$DB_PK\");\n";

    echo "\n\nfunction __construct(";
    foreach ($result as $key => $value) {
        if($key != 0) echo ",";
        echo "$".$value['COLUMN_NAME']."=";

        if($value['COLUMN_KEY'] == "PRI") {echo "NULL";}
        else if($value['IS_NULLABLE'] == "YES") {echo "NULL";}
        else 
        { 
            if(is_numeric($value['COLUMN_DEFAULT'])) 
            {
                echo $value['COLUMN_DEFAULT'];
            }else{
                if($value['COLUMN_DEFAULT'] == NULL) echo "'".$value['COLUMN_DEFAULT']."'" ;
                else echo $value['COLUMN_DEFAULT'] ;
            }
        } 
    }
    echo ")\n";

    echo "{\n";
        foreach ($result as $key => $value) {
            echo "  if (\$this->".$value['COLUMN_NAME']." == NULL) {\$this->".$value['COLUMN_NAME']." = \$".$value['COLUMN_NAME'].";}\n";
        }
    echo "}\n\n";

    echo "//  additional methods\n";

    echo "}\n";


    echo "\n?>";

echo "</pre>";
?>