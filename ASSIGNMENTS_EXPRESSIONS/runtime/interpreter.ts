import { NumberVal, RuntimeVal } from "./values.ts";
import {
    AssignmentExpr,
    BinaryExpr,
    Identifier,
    NumericLiteral,
    Program,
    Stmt,
    VarDeclaration,
} from "../frontend/ast.ts";
import Environment from "./environment.ts";
import { eval_program, eval_var_declaration } from "./eval/statements.ts";
import {
    eval_assignment,
    eval_binary_expr,
    eval_identifier,
} from "./eval/expressions.ts";

/**
 * Evaluates an AST (Abstract Syntax Tree) node within a given environment.
 * @param {Stmt} astNode - The AST node to evaluate.
 * @param {Environment} env - The runtime environment for evaluation.
 * @returns {RuntimeVal} The evaluated value of the AST node.
 */
export function evaluate(astNode: Stmt, env: Environment): RuntimeVal
{
    switch (astNode.kind)
    {
        case "NumericLiteral":
        {
            // Evaluate numeric literals
            return {
                value: (astNode as NumericLiteral).value,
                type: "number",
            } as NumberVal;
        }

        case "Identifier":
        {
            // Evaluate identifiers (variable lookups)
            return eval_identifier(astNode as Identifier, env);
        }

        case "AssignmentExpr":
        {
            // Evaluate assignment expressions
            return eval_assignment(astNode as AssignmentExpr, env);
        }

        case "BinaryExpr":
        {
            // Evaluate binary expressions
            return eval_binary_expr(astNode as BinaryExpr, env);
        }

        case "Program":
        {
            // Evaluate a program (root node)
            return eval_program(astNode as Program, env);
        }

        case "VarDeclaration":
        {
            // Evaluate variable declarations
            return eval_var_declaration(astNode as VarDeclaration, env);
        }

        default:
        {
            // Handle unsupported AST node types
            console.error(
                "This AST Node has not yet been implemented for evaluation:",
                astNode
            );
            Deno.exit(1); // Exit with error code
        }
    }
}
