<?php
// environment.php
    
    require_once 'values.php';
    
    function createGlobalEnv()
    {
        $env = new Environment();
        // Create Default Global Environment
        $env->declareVar('true', MK_BOOL(true), true);
        $env->declareVar('false', MK_BOOL(false), true);
        $env->declareVar('null', MK_NULL(), true);
        
        // Define a native builtin method
        $env->declareVar(
                'print',
                MK_NATIVE_FN(
                        function ($args, $scope)
                        {
                            foreach ($args as $arg)
                            {
                                echo $arg->value . ' ';
                            }
                            echo PHP_EOL;
                            return MK_NULL();
                        }
                ),
                true
        );
        
        $env->declareVar(
                'time',
                MK_NATIVE_FN(
                        function ($args, $env)
                        {
                            return MK_NUMBER(microtime(true) * 1000); // Multiplying to mimic milliseconds
                        }
                ),
                true
        );
        
        return $env;
    }
    
    class Environment
    {
        private $parent;
        private $variables; // associative array
        private $constants; // array of constant variable names
        
        public function __construct($parentENV = null)
        {
            $this->parent    = $parentENV;
            $this->variables = [];
            $this->constants = [];
        }
        
        public function declareVar($varname, $value, $constant)
        {
            if (array_key_exists($varname, $this->variables))
            {
                throw new Exception("Cannot declare variable $varname. It is already defined.");
            }
            $this->variables[$varname] = $value;
            if ($constant)
            {
                $this->constants[] = $varname;
            }
            return $value;
        }
        
        public function assignVar($varname, $value)
        {
            $env = $this->resolve($varname);
            // Cannot assign to constant
            if (in_array($varname, $env->constants))
            {
                throw new Exception("Cannot reassign variable $varname as it was declared constant.");
            }
            $env->variables[$varname] = $value;
            return $value;
        }
        
        public function resolve($varname)
        {
            if (array_key_exists($varname, $this->variables))
            {
                return $this;
            }
            if ($this->parent === null)
            {
                throw new Exception("Cannot resolve '$varname' as it does not exist.");
            }
            return $this->parent->resolve($varname);
        }
        
        public function lookupVar($varname)
        {
            $env = $this->resolve($varname);
            return $env->variables[$varname];
        }
    }
