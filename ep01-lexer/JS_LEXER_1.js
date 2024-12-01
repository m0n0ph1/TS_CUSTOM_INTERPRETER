const TokenType = {
    Number: 0,
    Identifier: 1,
    Let: 2,
    BinaryOperator: 3,
    Equals: 4,
    OpenParen: 5,
    CloseParen: 6
};

const KEYWORDS = {
    let: TokenType.Let
};

function token(value, type) {
    return {
        value: value,
        type: type
    };
}

function isalpha(src) {
    return src.toUpperCase() !== src.toLowerCase();
}

function isskippable(str) {
    return str === " " || str === "\n" || str === "\t";
}

function isint(str) {
    var c = str.charCodeAt(0);
    var bounds = ["0".charCodeAt(0), "9".charCodeAt(0)];
    return c >= bounds[0] && c <= bounds[1];
}

function tokenize(sourceCode) {
    var tokens = [];
    var src = sourceCode.split("");

    while (src.length > 0) {
        if (src[0] === "(") {
            tokens.push(token(src.shift(), TokenType.OpenParen));
        } else if (src[0] === ")") {
            tokens.push(token(src.shift(), TokenType.CloseParen));
        } else if (src[0] === "+" || src[0] === "-" || src[0] === "*" || src[0] === "/") {
            tokens.push(token(src.shift(), TokenType.BinaryOperator));
        } else if (src[0] === "=") {
            tokens.push(token(src.shift(), TokenType.Equals));
        } else {
            if (isint(src[0])) {
                var num = "";
                while (src.length > 0 && isint(src[0])) {
                    num += src.shift();
                }
                tokens.push(token(num, TokenType.Number));
            } else if (isalpha(src[0])) {
                var ident = "";
                while (src.length > 0 && isalpha(src[0])) {
                    ident += src.shift();
                }
                if (KEYWORDS.hasOwnProperty(ident)) {
                    tokens.push(token(ident, KEYWORDS[ident]));
                } else {
                    tokens.push(token(ident, TokenType.Identifier));
                }
            } else if (isskippable(src[0])) {
                src.shift();
            } else {
                console.error("Unrecognized character found in source: ", src[0].charCodeAt(0), src[0]);
                throw new Error("Unrecognized character: " + src[0]);
            }
        }
    }
    return tokens;
}