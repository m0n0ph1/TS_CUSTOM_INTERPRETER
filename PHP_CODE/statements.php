<?php
// statements.php
    
    require_once '../../frontend/ast.php';
    require_once '../environment.php';
    require_once '../interpreter.php';
    require_once '../values.php';
    
    function eval_program($program, $env)
    {
        $lastEvaluated = MK_NULL();
        foreach ($program->body as $statement)
        {
            $lastEvaluated = evaluate($statement, $env);
        }
        return $lastEvaluated;
    }
    
    function eval_var_declaration($declaration, $env)
    {
        $value = isset($declaration->value) ? evaluate($declaration->value, $env) : MK_NULL();
        return $env->declareVar($declaration->identifier, $value, $declaration->constant);
    }
    
    function eval_function_declaration($declaration, $env)
    {
        $fn = new FunctionValue(
                $declaration->name,
                $declaration->parameters,
                $declaration->body,
                $env
        );
        return $env->declareVar($declaration->name, $fn, true);
    }
