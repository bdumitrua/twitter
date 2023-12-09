import { ValidationValue } from "react-hook-form";

interface InputRule {
	value: string | number | RegExp | ValidationValue;
	message: string;
}

export interface InputRules {
	required?: InputRule | string;
	pattern?: InputRule;
	maxLength?: InputRule;
	minLength?: InputRule;
	validate?: (value: string) => boolean | string;
}
