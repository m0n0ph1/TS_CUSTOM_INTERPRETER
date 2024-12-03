<?php

    class NodeType
    {
        // STATEMENTS
        const PROGRAM              = 'Program';
        const VAR_DECLARATION      = 'VarDeclaration';
        const FUNCTION_DECLARATION = 'FunctionDeclaration';
		
        // EXPRESSIONS
        const ASSIGNMENT_EXPR 		= 'AssignmentExpr';
        const MEMBER_EXPR     		= 'MemberExpr';
        const CALL_EXPR       		= 'CallExpr';
		
        // LITERALS		
        const PROPERTY        		= 'Property';
        const OBJECT_LITERAL  		= 'ObjectLiteral';
        const NUMERIC_LITERAL 		= 'NumericLiteral';
        const IDENTIFIER      		= 'Identifier';
        const BINARY_EXPR     		= 'BinaryExpr';
    }

    abstract class Stmt
    {
        public $kind;
        public function __construct($kind)
        {
            $this->kind = $kind;
        }
    }

    class Program extends Stmt
    {
        public $body;
        public function __construct($body)
        {
            parent::__construct(NodeType::PROGRAM);
            $this->body = $body;
        }
    }
    
    class VarDeclaration extends Stmt
    {
        public $constant;   
        public $identifier; 
        public $value;      
        public function __construct($constant, $identifier, $value = null)
        {
            parent::__construct(NodeType::VAR_DECLARATION);
            $this->constant   = $constant;
            $this->identifier = $identifier;
            $this->value      = $value;
        }
    }
    
    class FunctionDeclaration extends Stmt
    {
        public $parameters; 
        public $name;       
        public $body;       
        public function __construct($name, $parameters, $body)
        {
            parent::__construct(NodeType::FUNCTION_DECLARATION);
            $this->name       = $name;
            $this->parameters = $parameters;
            $this->body       = $body;
        }
    }
    abstract class Expr extends Stmt
    {
        public function __construct($kind)
        {
            parent::__construct($kind);
        }
    }
    class AssignmentExpr extends Expr
    {
        public $assigne; 
        public $value;   
        
        public function __construct($assigne, $value)
        {
            parent::__construct(NodeType::ASSIGNMENT_EXPR);
            $this->assigne = $assigne;
            $this->value   = $value;
        }
    }
    class BinaryExpr extends Expr
    {
        public $left;     
        public $right;    
        public $operator; 
        
        public function __construct($left, $right, $operator)
        {
            parent::__construct(NodeType::BINARY_EXPR);
            $this->left     = $left;
            $this->right    = $right;
            $this->operator = $operator;
        }
    }
    
    class CallExpr extends Expr
    {
        public $args;   
        public $caller;
        
        public function __construct($caller, $args)
        {
            parent::__construct(NodeType::CALL_EXPR);
            $this->caller = $caller;
            $this->args   = $args;
        }
    }
    
    class MemberExpr extends Expr
    {
        public $object;   
        public $property;
        public $computed;
        
        public function __construct($object, $property, $computed)
        {
            parent::__construct(NodeType::MEMBER_EXPR);
            $this->object   = $object;
            $this->property = $property;
            $this->computed = $computed;
        }
    }
    class Identifier extends Expr
    {
        public $symbol;
        public function __construct($symbol)
        {
            parent::__construct(NodeType::IDENTIFIER);
            $this->symbol = $symbol;
        }
    }
    class NumericLiteral extends Expr
    {
        public $value;
        public function __construct($value)
        {
            parent::__construct(NodeType::NUMERIC_LITERAL);
            $this->value = $value;
        }
    }
    class Property extends Expr
    {
        public $key;   
        public $value; 
        public function __construct($key, $value = null)
        {
            parent::__construct(NodeType::PROPERTY);
            $this->key   = $key;
            $this->value = $value;
        }
    }
    
    class ObjectLiteral extends Expr
    {
        public $properties; // array of Property
        public function __construct($properties = [])
        {
            parent::__construct(NodeType::OBJECT_LITERAL);
            $this->properties = $properties;
        }
    }
