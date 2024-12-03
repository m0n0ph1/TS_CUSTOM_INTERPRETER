<?php 

class TokenType 
{
    // Whitespace Tokens
    const SPACE   = 'Space';    // Single space ' '
    const TAB     = 'Tab';      // Tab '\t'
    const NEWLINE = 'Newline';  // Newline '\n'

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

    // Operators
    const EQUALS           = 'Equals';          // =
    const BINARY_OPERATOR  = 'BinaryOperator';  // +, -, *, /

    // Others
    const IDENTIFIER       = 'Identifier';      // e.g., variable names
    const EOF              = 'EOF';             // End of file/input
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

// Main Program
$source = <<<CODE
let x = 10;
let y = 20;
print(x + y); // Should output 30
print(x * y); // Should output 200
CODE;

try 
{
    $tokens = tokenize($source);
    foreach ($tokens as $token) {
        echo "Token: Type = {$token->type}, Value = " . ($token->value ?? "null") . "\n";
    }
} 
catch (Exception $e) 
{
    echo "Error: " . $e->getMessage() . "\n";
}
