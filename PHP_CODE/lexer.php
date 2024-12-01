<?php

// lexer.php
    
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
    
    /**
     * Constant lookup for keywords and known identifiers + symbols.
     */
    $KEYWORDS = [
            'let'   => TokenType::LET,
            'const' => TokenType::CONST,
            'fn'    => TokenType::FN,
    ];

// Represents a single token from the source code.
    class Token
    {
        public $value; // contains the raw value as seen inside the source code.
        public $type;  // TokenType
        
        public function __construct($value = "", $type)
        {
            $this->value = $value;
            $this->type  = $type;
        }
    }

// Returns a token of a given type and value
    function create_token($value, $type)
    {
        return new Token($value, $type);
    }
    
    /**
     * Given a string representing source code: Produce tokens and handle
     * possible unidentified characters.
     *
     * - Returns an array of tokens.
     * - Does not modify the incoming string.
     */
    function tokenize($sourceCode)
    {
        global $KEYWORDS;
        $tokens = [];
        $src    = str_split($sourceCode);
        $length = count($src);
        $i      = 0;
        
        // Produce tokens until the EOF is reached.
        while ($i < $length)
        {
            $char = $src[$i];
            // BEGIN PARSING ONE CHARACTER TOKENS
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
                // Handle numeric literals -> Integers
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
                // Handle identifiers and keywords
                $ident = '';
                while ($i < $length && (ctype_alnum($src[$i]) || $src[$i] == '_'))
                {
                    $ident .= $src[$i];
                    $i++;
                }
                // CHECK FOR RESERVED KEYWORDS
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
                // Skip whitespace
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
