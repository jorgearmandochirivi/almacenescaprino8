<?php
class SIMReg
{
	//datos a encapsular
	static $_params = array();
	
	function setFromStructure( $params )
	{
		foreach( $params as $prop => $value )
			self::$_params[ $prop ] = $value;
		
		return true;		
	}
	
	function set( $name , $value )
	{
		self::$_params[ $name ] = $value;
		return true;
	}
	
	function get( $name )
	{
		return self::$_params[ $name ];
	}
}
?>
