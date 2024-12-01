<?php
// expressions.php
    
    require_once '../../frontend/ast.php';
    require_once '../environment.php';
    require_once '../interpreter.php';
    require_once '../values.php';
    
    function eval_numeric_binary_expr($lhs, $rhs, $operator)
    {
        if ($operator == '+')
        {
            $result = $lhs->value + $rhs->value;
        }
        elseif ($operator == '-')
        {
            $result = $lhs->value - $rhs->value;
        }
        elseif ($operator == '*')
        {
            $result = $lhs->value * $rhs->value;
        }
        elseif ($operator == '/')
        {
            if ($rhs->value == 0)
            {
                throw new Exception('Division by zero');
            }
            $result = $lhs->value / $rhs->value;
        }
        elseif ($operator == '%')
        {
            $result = $lhs->value % $rhs->value;
        }
        else
        {
            throw new Exception("Unsupported operator '$operator'");
        }
        return MK_NUMBER($result);
    }
    
    /**
     * Evaluates expressions following the binary operation type.
     */
    function eval_binary_expr($binop, $env)
    {
        $lhs = evaluate($binop->left, $env);
        $rhs = evaluate($binop->right, $env);
        // Only currently support numeric operations
        if ($lhs->type == 'number' && $rhs->type == 'number')
        {
            return eval_numeric_binary_expr($lhs, $rhs, $binop->operator);
        }
        // One or both are NULL
        return MK_NULL();
    }
    
    function eval_identifier($ident, $env)
    {
        return $env->lookupVar($ident->symbol);
    }
    
    function eval_assignment($node, $env)
    {
        if ($node->assigne->kind !== NodeType::IDENTIFIER)
        {
            throw new Exception("Invalid LHS inside assignment expr");
        }
        $varname = $node->assigne->symbol;
        return $env->assignVar($varname, evaluate($node->value, $env));
    }
    
    function eval_object_expr($obj, $env)
    {
        $object = new ObjectVal();
        foreach ($obj->properties as $property)
        {
            $key                      = $property->key;
            $value                    = isset($property->value) ? evaluate($property->value, $env) : $env->lookupVar($key);
            $object->properties[$key] = $value;
        }
        return $object;
    }
    
    function eval_call_expr($expr, $env)
    {
        $args = array_map(
                function ($arg) use ($env)
                {
                    return evaluate($arg, $env);
                }, $expr->args
        );
        $fn   = evaluate($expr->caller, $env);
        
        if ($fn->type == 'native-fn')
        {
            return $fn->call($args, $env);
        }
        if ($fn->type == 'function')
        {
            $func  = $fn;
            $scope = new Environment($func->declarationEnv);
            // Create the variables for the parameters list
            for ($i = 0; $i < count($func->parameters); $i++)
            {
                $varname = $func->parameters[$i];
                $scope->declareVar($varname, $args[$i] ?? MK_NULL(), false);
            }
            $result = MK_NULL();
            // Evaluate the function body line by line
            foreach ($func->body as $stmt)
            {
                $result = evaluate($stmt, $scope);
            }
            return $result;
        }
        throw new Exception("Cannot call value that is not a function");
    }
