import { RuntimeVal } from "./values.ts";

/**
 * Represents the runtime environment for variable declarations, lookups, and assignments.
 * Supports nested environments through parent scoping.
 */
export default class Environment
{
    private readonly parent?: Environment;              // Parent environment for nested scopes
    private variables: Map<string, RuntimeVal>; // Map to store variables and their values
    private constants: Set<string>;            // Set to track constant variable names

    /**
     * Constructs a new environment.
     * @param {Environment} [parentENV] - Optional parent environment for nested scoping.
     */
    constructor(parentENV?: Environment)
    {
        this.parent = parentENV;
        this.variables = new Map();
        this.constants = new Set();
    }

    /**
     * Declares a new variable in the current environment.
     * @param   {string}        varname     - The name of the variable to declare.
     * @param   {RuntimeVal}    value       - The initial value of the variable.
     * @param   {boolean}       constant    - Whether the variable is constant.
     * @returns {RuntimeVal}    The value of the declared variable.
     * @throws  {Error}         If the variable already exists in the current environment.
     */
    public declareVar(varname: string, value: RuntimeVal, constant: boolean) 
    {
        if (this.variables.has(varname))
        {
            throw new Error(`Cannot declare variable '${varname}'. It already exists.`);
        }

        this.variables.set(varname, value);

        if (constant)
        {
            this.constants.add(varname);
        }

        return value;
    }

    /**
     * Assigns a new value to an existing variable.
     * @param   {string}        varname - The name of the variable to assign a value to.
     * @param   {RuntimeVal}    value   - The new value to assign.
     * @returns {RuntimeVal}    The updated value of the variable.
     * @throws  {Error}         If the variable is constant or does not exist.
     */
    public assignVar(varname: string, value: RuntimeVal) 
    {
        const env = this.resolve(varname);

        if (env.constants.has(varname))
        {
            throw new Error(`Cannot reassign variable '${varname}' as it is declared constant.`);
        }

        env.variables.set(varname, value);
        return value;
    }

    /**
     * Retrieves the value of a variable.
     * @param {string} varname - The name of the variable to look up.
     * @returns {RuntimeVal} The value of the variable.
     * @throws {Error} If the variable does not exist.
     */
    public lookupVar(varname: string) 
    {
        const env = this.resolve(varname);
        return env.variables.get(varname) as RuntimeVal;
    }

    /**
     * Resolves the environment containing a variable.
     * @param {string} varname - The name of the variable to resolve.
     * @returns {Environment} The environment containing the variable.
     * @throws {Error} If the variable cannot be found in any scope.
     */
    public resolve(varname: string): Environment
    {
        if (this.variables.has(varname))
        {
            return this;
        }

        if (!this.parent)
        {
            throw new Error(`Cannot resolve variable '${varname}'. It does not exist.`);
        }

        return this.parent.resolve(varname);
    }
}
