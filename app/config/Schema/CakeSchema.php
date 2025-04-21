<?php
namespace Config\Schema;
require_once(__DIR__."/../../vendor/autoload.php");
/* CreateProducts fieldName:fieldType?[length]:indexType:indexName */
class Field {
	public $name;
	public $type;
	public $length;
	public $null = false;
	public $indexType;
	public $indexName;
	public $parameters;
	public function Field($name) {
		$this->name = $name;
	}
}
class Table {
	private function addAttrs($k, $v) {
		if(is_array($v)) {
			foreach($v as $subKey => $subValue) {
				if(is_int($subKey))
					addAttrs($k, $subValue);
				else
					addAttrs($subKey, $subValue);
			}
			return;
		}
		switch ($k) {
			case "type":
				$field->type = $v;
				break;
			case "null":
				$field->null = ($v == "true");
				break;
			case "length":
				$field->length = $v;
			case "default":
				break;
			case "unique":
				$field->indexType = "unique";
				break;
			case "key":
				if($v == "primary") $field->indexName = strtoupper($field->name)."_INDEX";
				break;
			default:
				$field->parameters[$k] = $v;
				break;
		}
	}
	public $name;
	public $fields;
	public function Table($name, $fields) {
		$this->name = $name;
		$this->fields = array();
		foreach($fields  as $fieldName => $fieldAttrs) {
			addField($fieldName, $fieldAttrs);
		}
	}
	public function addField($name, $attrs) {
		$field = new Field($fieldName);
		if(is_array($fieldAttrs)) {
			foreach($fieldAttrs as $f => $v) {
				addAttrs($f, $v);
			}
		}
		$this->fields[$fieldName] = $field;
	}
	/* CreateProducts fieldName:fieldType?[length]:indexType:indexName */
	public function bakePrint() {
		print "Create".ucfirst($this->name);
		foreach($this->fields as $field) {
			print " ".$field->name;
			if($field->type !== null) {
				print ":".$field->type;
				if($field->null) $field->type .= "?";
				if($field->length !== null) print "[".$field->length."]";
				if($field->indexType !== null) print ":".$field->indexType;
				if($field->indexName !== null) print ":".$field->indexName;
			}
		}
	}
}
class CakeSchema {
# empty
	public static function main(){
		$schema = new AppSchema();
		$schema->file = $argv[1];
		$reflect = new ReflectionObject($schema);
		$props   = $reflect->getProperties(ReflectionProperty::IS_PUBLIC);

		foreach ($props as $tableName => $fields) {
			if(is_array($fields)) {
				$table = new Table($tableName, $fields);
				print "bake migration ";
				$table->bakePrint();
				print "\n";
			}
		}
	}
}

