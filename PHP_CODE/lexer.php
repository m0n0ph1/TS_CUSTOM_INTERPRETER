<?php

	// lexer.php\
    class TokenType
    {
        // Literal Types
        const NUMBER     = 'Number';
        const IDENTIFIER = 'Identifier';
        
        // Keywords
        const LET   = 'Let';
        const CONST = 'Const';
        const FN    = 'Fn';
        
        // Grouping & Operators
        const BINARY_OPERATOR = 'BinaryOperator';
        const EQUALS          = 'Equals';
        const COMMA           = 'Comma';
        const DOT             = 'Dot';
        const COLON           = 'Colon';
        const SEMICOLON       = 'Semicolon';
        const OPEN_PAREN      = 'OpenParen';
        const CLOSE_PAREN     = 'CloseParen';
        const OPEN_BRACE      = 'OpenBrace';
        const CLOSE_BRACE     = 'CloseBrace';
        const OPEN_BRACKET    = 'OpenBracket';
        const CLOSE_BRACKET   = 'CloseBracket';
        const EOF             = 'EOF';
    }

    $KEYWORDS = 
	[
		'let'   => TokenType::LET,
		'const' => TokenType::CONST,
		'fn'    => TokenType::FN,
    ];

    class Token
    {
        public $value;
        public $type; 
        public function __construct($value, $type)
        {
            $this->value = $value;
            $this->type  = $type;
        }
    }

    function create_token($value, $type)
    {
        return new Token($value, $type);
    }

    function tokenize($sourceCode)
    {
        global $KEYWORDS;
        $tokens = [];
        $src    = str_split($sourceCode);
        $length = count($src);
        $i      = 0;

        while ($i < $length)
        {
            $char = $src[$i];

            if ($char == '(')
            {
                $tokens[] = create_token($char, TokenType::OPEN_PAREN);
                $i++;
            }
            elseif ($char == ')')
            {
                $tokens[] = create_token($char, TokenType::CLOSE_PAREN);
                $i++;
            }
            elseif ($char == '{')
            {
                $tokens[] = create_token($char, TokenType::OPEN_BRACE);
                $i++;
            }
            elseif ($char == '}')
            {
                $tokens[] = create_token($char, TokenType::CLOSE_BRACE);
                $i++;
            }
            elseif ($char == '[')
            {
                $tokens[] = create_token($char, TokenType::OPEN_BRACKET);
                $i++;
            }
            elseif ($char == ']')
            {
                $tokens[] = create_token($char, TokenType::CLOSE_BRACKET);
                $i++;
            }
            elseif (in_array($char, ['+', '-', '*', '/', '%']))
            {
                $tokens[] = create_token($char, TokenType::BINARY_OPERATOR);
                $i++;
            }
            elseif ($char == '=')
            {
                $tokens[] = create_token($char, TokenType::EQUALS);
                $i++;
            }
            elseif ($char == ';')
            {
                $tokens[] = create_token($char, TokenType::SEMICOLON);
                $i++;
            }
            elseif ($char == ':')
            {
                $tokens[] = create_token($char, TokenType::COLON);
                $i++;
            }
            elseif ($char == ',')
            {
                $tokens[] = create_token($char, TokenType::COMMA);
                $i++;
            }
            elseif ($char == '.')
            {
                $tokens[] = create_token($char, TokenType::DOT);
                $i++;
            }
            elseif (ctype_digit($char))
            {
                $num = '';
                while ($i < $length && ctype_digit($src[$i]))
                {
                    $num .= $src[$i];
                    $i++;
                }
                $tokens[] = create_token($num, TokenType::NUMBER);
            }
            elseif (ctype_alpha($char))
            {
                $ident = '';
                while ($i < $length && (ctype_alnum($src[$i]) || $src[$i] == '_'))
                {
                    $ident .= $src[$i];
                    $i++;
                }
                if (isset($KEYWORDS[$ident]))
                {
                    $tokens[] = create_token($ident, $KEYWORDS[$ident]);
                }
                else
                {
                    $tokens[] = create_token($ident, TokenType::IDENTIFIER);
                }
            }
            elseif (ctype_space($char))
            {
                $i++;
            }
            else
            {
                throw new Exception("Unrecognized character found in source: " . $char);
            }
        }
		
        $tokens[] = create_token('EndOfFile', TokenType::EOF);
		
        return $tokens;
    }
