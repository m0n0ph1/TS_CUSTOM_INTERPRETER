<?php
// main.php
    
    require_once './frontend/parser.php';
    require_once './runtime/environment.php';
    require_once './runtime/interpreter.php';
    
    run('./test.txt');
    
    function run($filename)
    {
        $parser  = new Parser();
        $env     = createGlobalEnv();
        $input   = file_get_contents($filename);
        $program = $parser->produceAST($input);
        $result  = evaluate($program, $env);
        
        // Optionally print the result
        print_r($result);
    }
