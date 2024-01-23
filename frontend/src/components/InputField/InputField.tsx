/* eslint-disable @typescript-eslint/no-explicit-any */
import styles from "@/assets/styles/components/InputField.module.scss";
import { InputRules } from "@/types/inputRules";

import { CSSProperties, useRef, useState } from "react";
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
	style?: CSSProperties;
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
	style,
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
	const [active, setActive] = useState(false);

	const inputRef = useRef<HTMLInputElement>(null);

	const onInputUnfocus = () => {
		if (inputRef.current && inputRef.current.value === "") {
			setActive(false);
		}
	};

	return (
		<>
			<div className={styles["input"]} style={style}>
				<label
					className={
						active
							? styles["input__label--active"]
							: styles["input__label"]
					}
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
							// placeholder={placeholder}
							className={`${styles["input__field"]} ${
								error ? styles["input__error"] : ""
							}`}
							{...field}
							onBlur={() => {
								field.onBlur();
								onInputUnfocus();
								trigger(name);
							}}
							ref={inputRef}
							onFocus={() => setActive(true)}
							required={required}
							disabled={disabled}
							maxLength={maxLength}
						/>
					)}
				/>
			</div>
			<ErrorMessage error={error} />
		</>
	);
};

export default InputField;
