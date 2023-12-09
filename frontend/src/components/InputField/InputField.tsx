/* eslint-disable @typescript-eslint/no-explicit-any */
import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { InputRules } from "@/types/inputRules";

import {
	Control,
	Controller,
	FieldPath,
	FieldValues,
	RegisterOptions,
} from "react-hook-form";
import { ErrorMessage } from "../ErrorMessage/ErrorMessage";

interface InputFieldProps<
	TFieldValues extends FieldValues = FieldValues,
	TName extends FieldPath<TFieldValues> = FieldPath<TFieldValues>
> {
	label: string;
	type: string;
	name?: string;
	id?: string;
	placeholder?: string;
	defaultValue?: string | null;
	rules?:
		| InputRules
		| Omit<RegisterOptions<TFieldValues, TName>, string | "disabled">;
	error?: string;
	control: Control<any>;
	trigger: (name: any) => void;
	maxLength?: number;
	required?: boolean;
	disabled?: boolean;
}

const InputField: React.FC<InputFieldProps> = ({
	label,
	type,
	name = type,
	id = name,
	placeholder = "",
	defaultValue = "",
	rules = {},
	error = "",
	control,
	trigger,
	maxLength,
	required = false,
	disabled = false,
}) => {
	return (
		<div className={styles["registration__input-container"]}>
			<label
				className={styles["registration__input-label"]}
				htmlFor={name}
			>
				{label}
			</label>
			<Controller
				name={name}
				control={control}
				defaultValue={defaultValue}
				rules={rules}
				render={({ field }) => (
					<input
						id={id}
						type={type}
						placeholder={placeholder}
						className={`${styles["registration__input"]} ${
							error ? styles["input__error"] : ""
						}`}
						{...field}
						onBlur={() => {
							field.onBlur();
							trigger(name);
						}}
						required={required}
						disabled={disabled}
						maxLength={maxLength}
					/>
				)}
			/>
			<ErrorMessage error={error} />
		</div>
	);
};

export default InputField;
