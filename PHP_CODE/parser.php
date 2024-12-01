<?php
// parser.php
    
    require_once './ast.php';
    require_once './lexer.php';
    
    class Parser
    {
        private $tokens  = [];
        private $current = 0;
        
        public function produceAST($sourceCode)
        {
            $this->tokens  = tokenize($sourceCode);
            $this->current = 0;
            $program       = new Program([]);
            
            while (!$this->isAtEnd())
            {
                $stmt = $this->parse_stmt();
                if ($stmt !== null)
                {
                    $program->body[] = $stmt;
                }
            }
            return $program;
        }
        
        private function isAtEnd()
        {
            return $this->current >= count($this->tokens) || $this->peek()->type === TokenType::EOF;
        }
        
        private function peek()
        {
            return $this->tokens[$this->current];
        }
        
        private function parse_stmt()
        {
            if ($this->match(TokenType::LET) || $this->match(TokenType::CONST))
            {
                return $this->parse_var_declaration();
            }
            elseif ($this->match(TokenType::FN))
            {
                return $this->parse_function_declaration();
            }
            else
            {
                return $this->parse_expr();
            }
        }
        
        private function match($type)
        {
            if ($this->check($type))
            {
                $this->advance();
                return true;
            }
            return false;
        }
        
        private function check($type)
        {
            if ($this->isAtEnd())
            {
                return false;
            }
            return $this->peek()->type === $type;
        }
        
        private function advance()
        {
            if (!$this->isAtEnd())
            {
                $this->current++;
            }
            return $this->previous();
        }
        
        private function previous()
        {
            return $this->tokens[$this->current - 1];
        }
        
        // Parsing functions (parse_stmt, parse_expr, etc.) would be implemented here

        private function parse_var_declaration()
        {
            $constant        = $this->previous()->type === TokenType::CONST;
            $identifierToken = $this->consume(TokenType::IDENTIFIER, "Expected variable name after 'let' or 'const'");
            $identifier      = $identifierToken->value;
            $value           = null;
            if ($this->match(TokenType::EQUALS))
            {
                $value = $this->parse_expr();
            }
            $this->consume(TokenType::SEMICOLON, "Expected ';' after variable declaration");
            return new VarDeclaration($constant, $identifier, $value);
        }
        
        private function consume($type, $message)
        {
            if ($this->check($type))
            {
                return $this->advance();
            }
            throw new Exception($message);
        }
        
        private function parse_expr()
        {
            return $this->parse_assignment();
        }
        
        private function parse_assignment()
        {
            $expr = $this->parse_equality();
            
            if ($this->match(TokenType::EQUALS))
            {
                $equals = $this->previous();
                $value  = $this->parse_assignment();
                
                if ($expr instanceof Identifier)
                {
                    return new AssignmentExpr($expr, $value);
                }
                
                throw new Exception("Invalid assignment target");
            }
            
            return $expr;
        }
        
        private function parse_equality()
        {
            $expr = $this->parse_addition();
            
            // Handle equality operators if any (e.g., ==, !=), currently not implemented
            
            return $expr;
        }
        
        private function parse_addition()
        {
            $expr = $this->parse_multiplication();
            
            while ($this->match(TokenType::BINARY_OPERATOR) && in_array($this->previous()->value, ['+', '-']))
            {
                $operator = $this->previous()->value;
                $right    = $this->parse_multiplication();
                $expr     = new BinaryExpr($expr, $right, $operator);
            }
            
            return $expr;
        }
        
        private function parse_multiplication()
        {
            $expr = $this->parse_unary();
            
            while ($this->match(TokenType::BINARY_OPERATOR) && in_array($this->previous()->value, ['*', '/', '%']))
            {
                $operator = $this->previous()->value;
                $right    = $this->parse_unary();
                $expr     = new BinaryExpr($expr, $right, $operator);
            }
            
            return $expr;
        }
        
        private function parse_unary()
        {
            // Implement unary operators if any
            return $this->parse_primary();
        }
        
        private function parse_primary()
        {
            if ($this->match(TokenType::NUMBER))
            {
                return new NumericLiteral(floatval($this->previous()->value));
            }
            if ($this->match(TokenType::IDENTIFIER))
            {
                return new Identifier($this->previous()->value);
            }
            if ($this->match(TokenType::OPEN_PAREN))
            {
                $expr = $this->parse_expr();
                $this->consume(TokenType::CLOSE_PAREN, "Expected ')' after expression");
                return $expr;
            }
            throw new Exception("Expected expression");
        }
        
        private function parse_function_declaration()
        {
            $nameToken = $this->consume(TokenType::IDENTIFIER, "Expected function name after 'fn'");
            $name      = $nameToken->value;
            $this->consume(TokenType::OPEN_PAREN, "Expected '(' after function name");
            $parameters = [];
            if (!$this->check(TokenType::CLOSE_PAREN))
            {
                do
                {
                    $paramToken   = $this->consume(TokenType::IDENTIFIER, "Expected parameter name");
                    $parameters[] = $paramToken->value;
                }
                while ($this->match(TokenType::COMMA));
            }
            $this->consume(TokenType::CLOSE_PAREN, "Expected ')' after parameters");
            $this->consume(TokenType::OPEN_BRACE, "Expected '{' before function body");
            $body = $this->parse_block();
            return new FunctionDeclaration($name, $parameters, $body);
        }
        
        private function parse_block()
        {
            $statements = [];
            while (!$this->check(TokenType::CLOSE_BRACE) && !$this->isAtEnd())
            {
                $statements[] = $this->parse_stmt();
            }
            $this->consume(TokenType::CLOSE_BRACE, "Expected '}' after block");
            return $statements;
        }
    }
