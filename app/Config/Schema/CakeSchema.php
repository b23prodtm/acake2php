<?php
namespace Config\Schema;
/** Cake App */
require_once(__DIR__."/../../../vendor/autoload.php");
/* CreateProducts fieldName:fieldType?[length]:indexType:indexName */
class Field {
	public $name;
	public $type;
	public $length;
	public $null = false;
	public $indexType;
	public $indexName;
	public $parameters;
	public function __construct($name) {
		$this->name = $name;
	}
	public function addAttrs($k, $v) {
		if(is_array($v)) {
			foreach($v as $subKey => $subValue) {
				if(is_int($subKey))
					$this->addAttrs($k, $subValue);
				else
					$this->addAttrs($subKey, $subValue);
			}
			return;
		}
		switch ($k) {
			case "type":
				$this->type = $v;
				break;
			case "null":
				$this->null = ($v == "true");
				break;
			case "length":
				$this->length = $v;
			case "default":
				break;
			case "unique":
				$this->indexType = "unique";
				break;
			case "key":
				if($v == "primary") $this->indexName = strtoupper($this->name)."_INDEX";
				break;
			default:
				$this->parameters[$k] = $v;
				break;
		}
	}
}
class Table {
	public $name;
	public $fields;
	public function __construct($name, $fields) {
		$this->name = $name;
		$this->fields = array();
		foreach($fields  as $fieldName => $fieldAttrs) {
			$this->addField($fieldName, $fieldAttrs);
		}
	}
	public function addField($name, $attrs) {
		$field = new Field($name);
		if(is_array($attrs)) {
			foreach($attrs as $f => $v) {
				$field->addAttrs($f, $v);
			}
		}
		$this->fields[] = $field;
	}
	/* CreateProducts fieldName:fieldType?[length]:indexType:indexName */
	public function bakePrint() {
		print "Create".ucfirst($this->name);
		foreach($this->fields as $k => $field) {
			if($field->type !== null) {
				print " ".$field->name;
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
	public static function main($argv) {
	        $file = $argv;
		if(is_array($argv)) {
			if(count($argv) < 2) { print "Usage: php __FILE__ <in-schemafile.php>"; return; }
			$file = $argv[1];
		}
		require_once($file);

		$schema = new AppSchema();
		$schema->file = $file;
		$reflect = new \ReflectionObject($schema);
		$props = $reflect->getProperties(\ReflectionProperty::IS_PUBLIC);

		foreach ($props as $k => $property) {
			$fields = $property->getValue($schema);
			if(!is_array($fields)) continue;
			$table = new Table($property->getName(), $fields);
			print "cake bake migration ";
			$table->bakePrint();
			print "\n";
		}
	}
}

