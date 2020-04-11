<?php 

class ADOBase {

    private function className(){
        return $this->class_name;
    }

    private function tableName(){
        return $this->table_name;
    }

    private function fields(){
        return implode(",", $this->fields);
    }

    private function pks_get(){
        
        return $this->pks;
    }

    private function pks($value){  
        $str_pk = "";
        if(count($this->pks) == 1){ // para cuando solo viene un id, ej. id=1 -> viene 1
            $str_pk = $this->pks[0]." = $value ";
        }
        else{
            $str_pk = $this->pks_assoc($value); // se deverá recibir un associativo con nombre idem que pk
        }
        return $str_pk;
    }

    // recibe array asociativo
    private function pks_assoc($value){  
        $str_pk = "";
            foreach($this->pks as $pk){
                if($str_pk !== "") {$str_pk.= " AND ";}
                $str_pk .= $pk." = ".$value[$pk]." "; // se deverá recibir un associativo con nombre idem que pk
            }
        
        return $str_pk;
    }

    private function mountSearch($search,$connection){
        $str = "";
        if (sizeof($search) > 0)
		{
			for ($i=0, $c=sizeof($search); $i<$c; $i++)
			{
                if($i!=0) $str .= " AND ";
                $str .= " ".$search[$i][0]." ".$search[$i][1]." ".$this->BindParam($search[$i][2],$connection);				
			}
        } 
        return $str;
    }



    protected function BindParam($param,$connection){
        switch (gettype($param))
        {
            case "string": return "'".$connection->real_escape_string($param)."'"; break;
            case "integer": 
            case "double":
            case "boolean" : return $param; break;
            default : return "null"; // for PKs in inserts
        }

    }


    //** $id si es una pk compuesta recibirá array */
    public function Get($id){
        $connection = DatabaseMySQL::Connect();
        $query = "SELECT ".$this->fields()." FROM ".$this->tableName()." WHERE 1 = 1 AND ".$this->pks($id);

        if ($result = DatabaseMySQL::Query($query,$connection)) {
            return DatabaseMySQL::ReadObject($result, $this->className());
        }
    }


    //** ej 1: $plist = $p->GetList(array(array("id",">",52)),"pais",true, "0,5"); */
    public function GetList($search = array(), $sortBy='', $ascending=true, $limit=''){
        $connection = DatabaseMySQL::Connect();
        $sqlLimit = ($limit != '' ? "LIMIT $limit" : '');
        $sqlSort = ($sortBy != '' ? "ORDER BY $sortBy ".($ascending ? "ASC" : "DESC") : "");
        $query = "SELECT ".$this->fields()." FROM ".$this->tableName()." WHERE 1 = 1 AND ".$this->mountSearch($search, $connection)." $sqlSort $sqlLimit";

        $itemList = Array();
        if ($result = DatabaseMySQL::Query($query,$connection)) {
            while ($item = DatabaseMySQL::ReadObject($result, $this->className())) {
                $itemList[] = $item;
            }
        }

        return $itemList;
    }

	public function Delete(){
		$connection = DatabaseMySQL::Connect();
        $query = "DELETE FROM ".$this->tableName()." WHERE ".$this->pks($this->id);
        $result = DatabaseMySQL::NonQuery($query, $connection);

		return $result;
    }

    public function Save(){
        $connection = DatabaseMySQL::Connect();
        $rows = 0;
        $query = "";
        
		if ($this->id != ''){
			$obj = $this->Get($this->id);
		}
		if ($obj)
		{

            $query= "UPDATE ".$this->tableName()." SET ";
            $querySet = "";
            foreach($this->fields as $field){
                if($querySet != "") $querySet .= ", ";
                $querySet .= "$field = ".$this->BindParam($this->$field,$connection);
            }

            $arrayPks = Array();
            foreach($this->pks_get() as $pk){
                $arrayPks[$pk] = $this-> $pk;
            }

            $queryWhere = " WHERE ".$this->pks_assoc($arrayPks);

            $query = $query.$querySet.$queryWhere;
		}
		else
		{
            $query = "INSERT INTO ".$this->tableName();
            $query .=" (";
            $query .= $this->fields();
            $query .=") VALUES (";
            $queryInto = "";
            foreach($this->fields as $field){
                if($queryInto != "") $queryInto .= ", ";
                $queryInto .= $this->BindParam($this->$field,$connection);
            }
            $query .= $queryInto.")";
        }
        
		$insertId = DatabaseMySQL::InsertOrUpdate($query, $connection);
		if ($this->id == "")
		{
			$this->id = $insertId;
        }
        
		return $this->id;
    }

    function SaveNew()
	{
        foreach($this->pks_get() as $pk){
            $this-> $pk = null;
        }
		return $this->Save();
	}

}

?>