<?php

/*

Class Method Sorting:
	* constants
	* protected $property (try to avoid)
	* private $field
	* abstract public
	* abstract protected
	* public static function create() (if any)
	* final public static
	* public static
	* protected static
	* private static
	* public function __construct (if any)
	* public fucntion __destruct (if any)
	* public fucntion __sleep (if any)
	* public fucntion __wakeup (if any)
	* final public
	* public
	* protected
	* private

Indenation: tab
Max width: 98 characters
No direct queries to models: encapsulate them in a static methods of the corresponding models

*/


/**
 * Some meaningful note about the class purpose. 
 * 
 * Brief list (maybe not complete) of methods to begin using class:
 *  * someOddMethodName() for odd things
 */
abstract class Corelib_Namespace_SomeClassName
{
	const CORRECT_ANSWER = 42; // must differ from zero
	
	private $privateVariable	= null;
	private $yetAnotherVariable	= null;
	
	abstract function overrideMe();
	
	final static function anotherOddMethod()
	{
		try {
			self::someOddMethodName(new FooImplementation());
		} 
		catch (FooException $e) {
			// do something
			throw $e; // try to not "eat" exceptions
		}
	}
	
	function someOddMethodName(FooInterface $someObject)
	{
		if (!$someObject) {
			TrashClass::staticMethod();
			throw new FooException("help me!");
		} 
		else {
			$someObject->shortMethod(
				$firstLongParameter,
				$secondLongParameter->anotherMethod(
					$foo,
					$bah
				)
			);
			
			// do something useful
		}
		
		$variable =
			$firstLongParameter
				? $secondLongParameter
				: $thirdLongParameter;
		
		if (
				$condition
				|| $incindent
				|| $whatEverElse
				|| (
					$one + $more * $complex / $condition
				)
			) {
			// bah!
		}
		
		$longString =
			'foo'
			.'bar'
			.'blah';
		
		// try to avoid casts
		$castedValue = (int) $rawVariable;
		
		return $this;
	}
	
	function methodWithWayTooLongArguments(
			OneClass $object, 
			AnotherClass $anotherObject, 
			Third_Hude_Class_Name $thirdClassName
		)
	{
		$result =
			$object
				->setAnother($anotherObject)
				->doSomethingPrivate($somethingElse);
				
		return $this->processResult($result);
	}
	
	private function codeConstructions()
	{
		switch ($foo) {
			case $boo: {
				$this->fooBar();
				
				$that->fooBlah();
				
				break;
			}
			
			case $zoo: {
				$that->phew($this);
				
				break;
			}
			
			default: {
				die();
				
				break;
			}
		}
		
		trigger_error("Should not be happened", E_USER_ERROR);
	}
}
