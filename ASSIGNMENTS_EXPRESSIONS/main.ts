import Parser               from "./frontend/parser.ts";
import Environment          from "./runtime/environment.ts";
import { evaluate }         from "./runtime/interpreter.ts";
import { MK_BOOL, MK_NULL } from "./runtime/values.ts";

// Main REPL Function
function repl()
{
    // Initialize the parser and environment
    const parser = new Parser();
    const env = new Environment();

    // Declare global constants in the environment
    env.declareVar("true", MK_BOOL(true), true);   // Boolean true
    env.declareVar("false", MK_BOOL(false), true); // Boolean false
    env.declareVar("null", MK_NULL(), true);       // Null value

    // Start REPL prompt
    console.log("\nRepl v0.1");

    // Infinite loop for REPL
    while (true)
    {
        // Get user input
        const input = prompt("> ");

        // Exit condition: No input or 'exit' keyword
        if (!input || input.includes("exit"))
        {
            console.log("Exiting REPL. Goodbye!");
            Deno.exit(0); // Exit gracefully with status code 0
        }

        try
        {
            // Parse input into an AST (Abstract Syntax Tree)
            const program = parser.produceAST(input);

            // Evaluate the program in the current environment
            const result = evaluate(program, env);

            // Output the result of evaluation
            console.log(result);
        }
        catch (error)
        {
            // Handle any runtime or syntax errors
            console.error("Error:", error.message);
        }
    }
}

// Start the REPL
repl();
