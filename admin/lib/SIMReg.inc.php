<?php
class SIMReg
{
	//datos a encapsular
	static array $_params = array();
	
	static function setFromStructure( array $params ): bool
	{
		foreach( $params as $prop => $value )
			self::$_params[ $prop ] = $value;
		
		return true;		
	}
	
	static function set( string $name , mixed $value ): bool
	{
		self::$_params[ $name ] = $value;
		return true;
	}
	
	static function get( string $name ): mixed
	{
		return self::$_params[ $name ] ?? null;
	}
}
?>
