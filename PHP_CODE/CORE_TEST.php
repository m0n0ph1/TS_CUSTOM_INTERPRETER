<?php 

require_once 'parser.php';
require_once 'environment.php';
require_once 'interpreter.php';

function runTest($filename)
{
    echo "Running test: $filename\n";
	
    $parser  = new Parser();
    $env     = createGlobalEnv();
    $input   = file_get_contents($filename);
    
    try 
	{
        $program = $parser->produceAST($input);
        $result  = evaluate($program, $env);
		
        echo "Result: ";
        print_r($result);
        echo "\n\n";
    } 
	catch (Exception $e) 
	{
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}

$testFiles = glob('tests.txt');

foreach ($testFiles as $testFile) 
{
    runTest($testFile);
}
