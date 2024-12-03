<?php
// interpreter.php
    
    require_once 'values.php';
    require_once 'ast.php';
    require_once 'environment.php';
    require_once 'statements.php';
    require_once 'expressions.php';
    
    function evaluate($astNode, $env)
    {
        switch ($astNode->kind)
        {
            case NodeType::NUMERIC_LITERAL:
                return MK_NUMBER($astNode->value);
            case NodeType::IDENTIFIER:
                return eval_identifier($astNode, $env);
            case NodeType::OBJECT_LITERAL:
                return eval_object_expr($astNode, $env);
            case NodeType::CALL_EXPR:
                return eval_call_expr($astNode, $env);
            case NodeType::ASSIGNMENT_EXPR:
                return eval_assignment($astNode, $env);
            case NodeType::BINARY_EXPR:
                return eval_binary_expr($astNode, $env);
            case NodeType::PROGRAM:
                return eval_program($astNode, $env);
            // Handle statements
            case NodeType::VAR_DECLARATION:
                return eval_var_declaration($astNode, $env);
            case NodeType::FUNCTION_DECLARATION:
                return eval_function_declaration($astNode, $env);
            // Handle unimplemented ast types as error.
            default:
                throw new Exception("Unhandled AST Node kind: " . $astNode->kind);
        }
    }
