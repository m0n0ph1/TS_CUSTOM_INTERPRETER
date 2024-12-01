<?php
// values.php
    
    require_once './environment.php';
    require_once '../frontend/ast.php';
    
    abstract class RuntimeVal
    {
        public $type;
        
        public function __construct($type)
        {
            $this->type = $type;
        }
    }
    
    /**
     * Defines a value of undefined meaning.
     */
    class NullVal extends RuntimeVal
    {
        public $value;
        
        public function __construct()
        {
            parent::__construct('null');
            $this->value = null;
        }
    }
    
    function MK_NULL()
    {
        return new NullVal();
    }
    
    class BooleanVal extends RuntimeVal
    {
        public $value;
        
        public function __construct($value = true)
        {
            parent::__construct('boolean');
            $this->value = $value;
        }
    }
    
    function MK_BOOL($b = true)
    {
        return new BooleanVal($b);
    }
    
    class NumberVal extends RuntimeVal
    {
        public $value;
        
        public function __construct($value = 0)
        {
            parent::__construct('number');
            $this->value = $value;
        }
    }
    
    function MK_NUMBER($n = 0)
    {
        return new NumberVal($n);
    }
    
    class ObjectVal extends RuntimeVal
    {
        public $properties; // associative array
        
        public function __construct($properties = [])
        {
            parent::__construct('object');
            $this->properties = $properties;
        }
    }
    
    class NativeFnValue extends RuntimeVal
    {
        public $call; // callable
        
        public function __construct($call)
        {
            parent::__construct('native-fn');
            $this->call = $call;
        }
    }
    
    function MK_NATIVE_FN($call)
    {
        return new NativeFnValue($call);
    }
    
    class FunctionValue extends RuntimeVal
    {
        public $name;
        public $parameters;     // array of strings
        public $body;           // array of Stmt
        public $declarationEnv; // Environment
        
        public function __construct($name, $parameters, $body, $declarationEnv)
        {
            parent::__construct('function');
            $this->name           = $name;
            $this->parameters     = $parameters;
            $this->body           = $body;
            $this->declarationEnv = $declarationEnv;
        }
    }
