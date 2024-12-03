<?php 

	class TokenType 
	{
		// Grouping Tokens
		const OPEN_BRACE       = 'OpenBrace';        // {
		const CLOSE_BRACE      = 'CloseBrace';       // }
		const OPEN_BRACKET     = 'OpenBracket';      // [
		const CLOSE_BRACKET    = 'CloseBracket';     // ]
		const OPEN_PAREN       = 'OpenParen';        // (
		const CLOSE_PAREN      = 'CloseParen';       // )
		
		// Delimiters
		const COMMA            = 'Comma';           // ,
		const DOT              = 'Dot';             // .
		const SEMICOLON        = 'Semicolon';       // ;

		// Literals
		const NUMBER           = 'Number';          // e.g., 123
		const STRING           = 'String';          // e.g., "hello", 'world'

		// Keywords
		const LET              = 'Let';             // let
		const CONST            = 'Const';           // const
		const PRINT            = 'Print';           // print
		const IF               = 'If';              // if
		const ELSE             = 'Else';            // else
		const FOR              = 'For';             // for
		const WHILE            = 'While';           // while
		const FUNCTION         = 'Function';        // fn, function

		// Operators
		const EQUALS           = 'Equals';          // =
		const BINARY_OPERATOR  = 'BinaryOperator';  // +, -, *, /
		const LOGICAL_OPERATOR = 'LogicalOperator'; // &&, ||, !
		const COMPARISON       = 'Comparison';      // ==, !=, >, <, >=, <=
		const ARROW            = 'Arrow';           // =>

		// Others
		const IDENTIFIER       = 'Identifier';      // e.g., variable names
		const EOF              = 'EOF';             // End of file/input
		
		// AST Node Types
		const PROGRAM              = 'Program';
		const VAR_DECLARATION      = 'VarDeclaration';
		const FUNCTION_DECLARATION = 'FunctionDeclaration';
		const ASSIGNMENT_EXPR      = 'AssignmentExpr';
		const MEMBER_EXPR          = 'MemberExpr';
		const CALL_EXPR            = 'CallExpr';    
		const PROPERTY             = 'Property';
		const OBJECT_LITERAL       = 'ObjectLiteral';
		const NUMERIC_LITERAL      = 'NumericLiteral';
		const STRING_LITERAL       = 'StringLiteral';
		const BINARY_EXPR          = 'BinaryExpr';
		const LOGICAL_EXPR         = 'LogicalExpr';
	}
	
	class Token 
{
    public $type;
    public $value;

    public function __construct($type, $value = null) 
    {
        $this->type = $type;
        $this->value = $value;
    }
}
	
function tokenize($source) {
    $tokens = [];
    $src = str_split($source);
    $i = 0;
    $line = 1; // Line number tracker (optional)

    while ($i < count($src)) {
        $char = $src[$i];

        // Handle newlines
        if ($char === "\n") {
            $tokens[] = new Token(TokenType::NEWLINE, "\n");
            $line++; // Increment line count
            $i++;
            continue;
        }

        // Handle tabs
        if ($char === "\t") {
            $tokens[] = new Token(TokenType::TAB, "\t");
            $i++;
            continue;
        }

        // Handle spaces
        if ($char === " ") {
            $tokens[] = new Token(TokenType::SPACE, " ");
            $i++;
            continue;
        }

        // Handle numbers
        if (ctype_digit($char)) {
            $num = '';
            while ($i < count($src) && ctype_digit($src[$i])) {
                $num .= $src[$i++];
            }
            $tokens[] = new Token(TokenType::NUMBER, intval($num));
            continue;
        }

        // Handle identifiers and keywords
        if (ctype_alpha($char)) {
            $ident = '';
            while ($i < count($src) && ctype_alnum($src[$i])) {
                $ident .= $src[$i++];
            }
            if ($ident === 'let') {
                $tokens[] = new Token(TokenType::LET);
            } elseif ($ident === 'print') {
                $tokens[] = new Token(TokenType::PRINT);
            } else {
                $tokens[] = new Token(TokenType::IDENTIFIER, $ident);
            }
            continue;
        }

        // Handle single-character tokens
        if ($char === '=') {
            $tokens[] = new Token(TokenType::EQUALS);
        } elseif ($char === ';') {
            $tokens[] = new Token(TokenType::SEMICOLON);
        } elseif (in_array($char, ['+', '-', '*', '/'])) {
            $tokens[] = new Token(TokenType::BINARY_OPERATOR, $char);
        } elseif ($char === '{') {
            $tokens[] = new Token(TokenType::OPEN_BRACE, $char);
        } elseif ($char === '}') {
            $tokens[] = new Token(TokenType::CLOSE_BRACE, $char);
        } elseif ($char === '[') {
            $tokens[] = new Token(TokenType::OPEN_BRACKET, $char);
        } elseif ($char === ']') {
            $tokens[] = new Token(TokenType::CLOSE_BRACKET, $char);
        } elseif ($char === ',') {
            $tokens[] = new Token(TokenType::COMMA, $char);
        } elseif ($char === '.') {
            $tokens[] = new Token(TokenType::DOT, $char);
        } else {
            throw new Exception("Unexpected character: $char on line $line");
        }

        $i++;
    }

    $tokens[] = new Token(TokenType::EOF);
    return $tokens;
}

// Parser
class Parser 
{
    private $tokens;
    private $current = 0;

    public function __construct($tokens) 
	{
        $this->tokens = $tokens;
    }

    private function peek() 
	{
        return $this->tokens[$this->current];
    }

    private function advance() 
	{
        return $this->tokens[$this->current++];
    }

	
    private function check($type) 
	{
        return $this->peek()->type === $type;
    }

    private function consume($type, $message) 
	{
        if ($this->check($type)) 
		{
            return $this->advance();
        }
        throw new Exception($message);
    }

    public function parse() 
	{
        $statements = [];
        while (!$this->check(TokenType::EOF)) 
		{
            $statements[] = $this->parse_statement();
        }
        return $statements;
    }

    private function parse_statement() {
        if ($this->check(TokenType::LET)) {
            return $this->parse_var_declaration();
        } elseif ($this->check(TokenType::PRINT)) {
            return $this->parse_print_statement();
        }
        throw new Exception("Unknown statement.");
    }

    private function parse_var_declaration() {
        $this->consume(TokenType::LET, "Expected 'let'");
        $identifier = $this->consume(TokenType::IDENTIFIER, "Expected variable name")->value;
        $this->consume(TokenType::EQUALS, "Expected '='");
        $value = $this->parse_expression();
        $this->consume(TokenType::SEMICOLON, "Expected ';'");
        return ['type' => 'VarDeclaration', 'name' => $identifier, 'value' => $value];
    }

    private function parse_print_statement() {
        $this->consume(TokenType::PRINT, "Expected 'print'");
        $value = $this->parse_expression();
        $this->consume(TokenType::SEMICOLON, "Expected ';'");
        return ['type' => 'Print', 'value' => $value];
    }

    private function parse_expression() {
        $left = $this->parse_primary();

        while ($this->check(TokenType::BINARY_OPERATOR)) {
            $operator = $this->advance()->value;
            $right = $this->parse_primary();
            $left = ['type' => 'BinaryExpression', 'left' => $left, 'operator' => $operator, 'right' => $right];
        }

        return $left;
    }

    private function parse_primary() {
        if ($this->check(TokenType::NUMBER)) {
            return ['type' => 'NumericLiteral', 'value' => $this->advance()->value];
        } elseif ($this->check(TokenType::IDENTIFIER)) {
            return ['type' => 'Identifier', 'value' => $this->advance()->value];
        }
        throw new Exception("Expected an expression.");
    }
}

// Environment
class Environment {
    private $variables = [];

    public function declare($name, $value) {
        $this->variables[$name] = $value;
    }

    public function get($name) {
        if (!isset($this->variables[$name])) {
            throw new Exception("Undefined variable: $name");
        }
        return $this->variables[$name];
    }
}

// Interpreter
function interpret($statements, $env) {
    foreach ($statements as $stmt) {
        if ($stmt['type'] === 'VarDeclaration') {
            $value = evaluate_expression($stmt['value'], $env);
            $env->declare($stmt['name'], $value);
        } elseif ($stmt['type'] === 'Print') {
            $value = evaluate_expression($stmt['value'], $env);
            echo $value . "\n";
        }
    }
}

function evaluate_expression($expr, $env) {
    if ($expr['type'] === 'NumericLiteral') {
        return $expr['value'];
    } elseif ($expr['type'] === 'Identifier') {
        return $env->get($expr['value']);
    } elseif ($expr['type'] === 'BinaryExpression') {
        $left = evaluate_expression($expr['left'], $env);
        $right = evaluate_expression($expr['right'], $env);
        switch ($expr['operator']) {
            case '+': return $left + $right;
            case '-': return $left - $right;
            case '*': return $left * $right;
            case '/': return $left / $right;
        }
    }
    throw new Exception("Unknown expression type.");
}

// Main Program
$source = <<<CODE
let x = 10;
let y = 20;
print(x + y); // Should output 30
print(x * y); // Should output 200
CODE;

try 
{
    $tokens 	= tokenize($source);
    $parser 	= new Parser($tokens);
    $statements = $parser->parse();
    $env 		= new Environment();
	
    interpret($statements, $env);
} 
catch (Exception $e) 
{
    echo "Error: " . $e->getMessage() . "\n";
}

