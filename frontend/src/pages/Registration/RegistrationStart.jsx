import styles from "@/assets/styles/pages/Auth/Registration.scss";
import React, { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { startRegisterAsync } from "../../redux/slices/register.slice";
import { emailRules, nameRules } from "../../utils/inputRules";

const RegistrationStart = () => {
	const {
		control,
		trigger,
		setError,
		handleSubmit,
		formState: { errors },
	} = useForm();
	const navigate = useNavigate();
	const dispatch = useDispatch();

	const handleStartRegistration = async (data) => {
		console.log(data);
		const response = await dispatch(
			startRegisterAsync({
				name: data.name,
				email: data.email,
				birth_date: data.birth_date,
			})
		);
		if (!response.error) {
			const queryParams = new URLSearchParams({
				name: data.name,
				email: data.email,
				birth_date: data.birth_date,
			}).toString();
			navigate(
				`/registration/confirm/${response.payload}?${queryParams}`
			);
		}
	};

	const error = useSelector((state) => state.register.error);
	useEffect(() => {
		if (error && error.status === 422) {
			setError("email", {
				type: "manual",
				message: "Данная почта уже занята",
			});
		}
	}, [error]);

	return (
		<form
			onSubmit={handleSubmit(handleStartRegistration)}
			className={styles["registration__form"]}
		>
			<InputField
				label="Имя"
				type="text"
				name="name"
				error={errors.name}
				placeholder="Имя"
				rules={nameRules}
				trigger={trigger}
				control={control}
				required={true}
			/>
			<InputField
				label="Почта"
				type="email"
				name="email"
				error={errors.email}
				placeholder="Почта"
				rules={emailRules}
				trigger={trigger}
				control={control}
				required={true}
			/>
			<InputField
				label="Дата рождения"
				type="date"
				name="birth_date"
				error={errors.birthDate}
				rules={{
					required: "Дата обязательна к заполнению.",
				}}
				trigger={trigger}
				control={control}
				required={true}
			/>
			<button className={styles["registration__button"]} type="submit">
				Зарегистрироваться
			</button>
		</form>
	);
};

export default RegistrationStart;