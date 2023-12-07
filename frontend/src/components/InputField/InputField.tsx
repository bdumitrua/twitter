/* eslint-disable @typescript-eslint/no-explicit-any */
import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { InputRules } from "@/types/inputRules";
import React from "react";
import {
	Control,
	Controller,
	FieldError,
	FieldErrorsImpl,
	Merge,
} from "react-hook-form";
import { ErrorMessage } from "../ErrorMessage/ErrorMessage";

interface InputFieldProps {
	label: string;
	type: string;
	name?: string;
	id?: string;
	placeholder?: string;
	defaultValue?: string | null;
	rules?: InputRules;
	error?: FieldError | string | Merge<FieldError, FieldErrorsImpl<any>>;
	control: Control<any>;
	trigger: () => void;
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
						onBlur={(e) => {
							field.onBlur(e);
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
