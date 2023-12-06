import styles from "@/assets/styles/pages/Auth/Registration.scss";
import React from "react";
import { Controller } from "react-hook-form";
import { ErrorMessage } from "../ErrorMessage/ErrorMessage";

const InputField = ({
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
