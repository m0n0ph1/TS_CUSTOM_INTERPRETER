<?php
// ast.php

// Define the NodeType class with constants for each node type
    class NodeType
    {
        // STATEMENTS
        const PROGRAM              = 'Program';
        const VAR_DECLARATION      = 'VarDeclaration';
        const FUNCTION_DECLARATION = 'FunctionDeclaration';
        // EXPRESSIONS
        const ASSIGNMENT_EXPR = 'AssignmentExpr';
        const MEMBER_EXPR     = 'MemberExpr';
        const CALL_EXPR       = 'CallExpr';
        // LITERALS
        const PROPERTY        = 'Property';
        const OBJECT_LITERAL  = 'ObjectLiteral';
        const NUMERIC_LITERAL = 'NumericLiteral';
        const IDENTIFIER      = 'Identifier';
        const BINARY_EXPR     = 'BinaryExpr';
    }
    
    /**
     * Statements do not result in a value at runtime.
     * They contain one or more expressions internally.
     */
    abstract class Stmt
    {
        public $kind;
        
        public function __construct($kind)
        {
            $this->kind = $kind;
        }
    }
    
    /**
     * Defines a block which contains many statements.
     * - Only one program will be contained in a file.
     */
    class Program extends Stmt
    {
        public $body; // array of Stmt
        
        public function __construct($body)
        {
            parent::__construct(NodeType::PROGRAM);
            $this->body = $body;
        }
    }
    
    class VarDeclaration extends Stmt
    {
        public $constant;   // boolean
        public $identifier; // string
        public $value;      // Expr|null
        
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
        public $parameters; // array of strings
        public $name;       // string
        public $body;       // array of Stmt
        
        public function __construct($name, $parameters, $body)
        {
            parent::__construct(NodeType::FUNCTION_DECLARATION);
            $this->name       = $name;
            $this->parameters = $parameters;
            $this->body       = $body;
        }
    }
    
    /** Expressions will result in a value at runtime unlike Statements */
    abstract class Expr extends Stmt
    {
        public function __construct($kind)
        {
            parent::__construct($kind);
        }
    }
    
    class AssignmentExpr extends Expr
    {
        public $assigne; // Expr
        public $value;   // Expr
        
        public function __construct($assigne, $value)
        {
            parent::__construct(NodeType::ASSIGNMENT_EXPR);
            $this->assigne = $assigne;
            $this->value   = $value;
        }
    }
    
    /**
     * An operation with two sides separated by an operator.
     * Both sides can be ANY Complex Expression.
     * - Supported Operators: + | - | / | * | %
     */
    class BinaryExpr extends Expr
    {
        public $left;     // Expr
        public $right;    // Expr
        public $operator; // string
        
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
        public $args;   // array of Expr
        public $caller; // Expr
        
        public function __construct($caller, $args)
        {
            parent::__construct(NodeType::CALL_EXPR);
            $this->caller = $caller;
            $this->args   = $args;
        }
    }
    
    class MemberExpr extends Expr
    {
        public $object;   // Expr
        public $property; // Expr
        public $computed; // boolean
        
        public function __construct($object, $property, $computed)
        {
            parent::__construct(NodeType::MEMBER_EXPR);
            $this->object   = $object;
            $this->property = $property;
            $this->computed = $computed;
        }
    }

// LITERAL / PRIMARY EXPRESSION TYPES
    
    /**
     * Represents a user-defined variable or symbol in source.
     */
    class Identifier extends Expr
    {
        public $symbol; // string
        
        public function __construct($symbol)
        {
            parent::__construct(NodeType::IDENTIFIER);
            $this->symbol = $symbol;
        }
    }
    
    /**
     * Represents a numeric constant inside the source code.
     */
    class NumericLiteral extends Expr
    {
        public $value; // number
        
        public function __construct($value)
        {
            parent::__construct(NodeType::NUMERIC_LITERAL);
            $this->value = $value;
        }
    }
    
    class Property extends Expr
    {
        public $key;   // string
        public $value; // Expr|null
        
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
