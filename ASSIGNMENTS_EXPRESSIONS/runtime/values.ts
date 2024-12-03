// Define possible runtime value types
export type ValueType = "null" | "number" | "boolean";

/**
 * Base interface for all runtime values.
 */
export interface RuntimeVal
{
    type: ValueType; // The type of the value (e.g., "null", "number", "boolean")
}

/**
 * Null Value Interface
 * Represents a runtime value with undefined meaning.
 */
export interface NullVal extends RuntimeVal
{
    type: "null";   // Type explicitly set to "null"
    value: null;    // Always null
}

/**
 * Factory function to create a NullVal.
 * @returns {NullVal} An object representing a null runtime value.
 */
export function MK_NULL() {
    return {
        type: "null",
        value: null
    } as NullVal;
}

/**
 * Boolean Value Interface
 * Represents a runtime value that holds a boolean.
 */
export interface BooleanVal extends RuntimeVal
{
    type: "boolean"; // Type explicitly set to "boolean"
    value: boolean;  // Holds a boolean value
}

/**
 * Factory function to create a BooleanVal.
 * @param {boolean} [b=true] - The boolean value to initialize with (default: true).
 * @returns {BooleanVal} An object representing a boolean runtime value.
 */
export function MK_BOOL(b = true) {
    return {
        type: "boolean",
        value: b
    } as BooleanVal;
}

/**
 * Number Value Interface
 * Represents a runtime value that holds a number.
 */
export interface NumberVal extends RuntimeVal
{
    type: "number"; // Type explicitly set to "number"
    value: number;  // Holds a numeric value
}

/**
 * Factory function to create a NumberVal.
 * @param {number} [n=0] - The number to initialize with (default: 0).
 * @returns {NumberVal} An object representing a numeric runtime value.
 */
export function MK_NUMBER(n = 0) {
    return {
        type: "number",
        value: n
    } as NumberVal;
}
