import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { formatDate } from "@/utils/functions/formatDate.js";
import React from "react";
import { useForm } from "react-hook-form";
import { useLocation, useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { getLastEntry } from "../../utils/functions/getLastEntry";

const RegistrationConfirmation = () => {
	const {
		control,
		handleSubmit,
		formState: { errors },
	} = useForm();

	const location = useLocation();
	const navigate = useNavigate();
	const registrationId = getLastEntry(location.pathname, "/");

	const queryParams = new URLSearchParams(location.search);
	const name = queryParams.get("name");
	const email = queryParams.get("email");
	const birthDate = queryParams.get("birth_date");

	const handleConfirmRegistration = () => {
		navigate(`/registration/code/${registrationId}`);
	};

	return (
		<form
			className={styles["registration__form"]}
			onSubmit={handleSubmit(handleConfirmRegistration)}
		>
			<InputField
				label="Имя"
				type="text"
				name="name"
				defaultValue={name}
				error={errors.name}
				control={control}
				disabled={true}
			/>
			<InputField
				label="Почта"
				type="email"
				name="email"
				defaultValue={email}
				error={errors.email}
				control={control}
				disabled={true}
			/>
			<InputField
				label="Дата рождения"
				type="text"
				name="birth_date"
				defaultValue={formatDate(birthDate)}
				error={errors.birthDate}
				control={control}
				disabled={true}
			/>
			<button className={styles["registration__button"]} type="submit">
				Далее
			</button>
		</form>
	);
};

export default RegistrationConfirmation;
