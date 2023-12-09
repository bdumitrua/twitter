import { InputRules } from "@/types/inputRules";

export const emailRules: InputRules = {
	required: "Почта является обязательным полем.",
	pattern: {
		value: /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i,
		message: "Введена некорректная почта.",
	},
	maxLength: {
		value: 255,
		message: "Длина почты может быть не более 255 символов.",
	},
};

export const passwordRules: InputRules = {
	required: "Пароль обязателен",
	minLength: {
		value: 8,
		message: "Пароль должен содержать не менее 8 символов",
	},
};

export const nameRules: InputRules = {
	required: "Имя является обязательным полем.",
	minLength: {
		value: 2,
		message: "Имя не может быть короче 2 символов.",
	},
	maxLength: {
		value: 255,
		message: "Имя может быть не длиннее 255 символов.",
	},
};

export const codeRules: InputRules = {
	required: "Введите код подтверждения",
	minLength: {
		value: 5,
		message: "Код подтверждения должен состоять из 5 символов",
	},
};
