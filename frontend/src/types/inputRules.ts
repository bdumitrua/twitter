interface InputRule {
	value?: string | RegExp | number;
	message: string;
}

export interface InputRules {
	required?: string | InputRule;
	pattern?: InputRule;
	maxLength?: InputRule;
	minLength?: InputRule;
	validate?: (value: string) => boolean | string;
}
