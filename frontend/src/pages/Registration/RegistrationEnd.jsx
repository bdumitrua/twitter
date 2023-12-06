import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { ErrorMessage } from "@/components/ErrorMessage/ErrorMessage.jsx";
import { registerAsync } from "@/redux/slices/register.slice";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { getLastEntry } from "../../utils/functions/getLastEntry";
import { passwordRules } from "../../utils/inputRules";

const RegistrationEnd = () => {
	const {
		control,
		handleSubmit,
		watch,
		setError,
		trigger,
		formState: { errors },
	} = useForm();

	const navigate = useNavigate();
	const dispatch = useDispatch();

	const registrationId = getLastEntry(location.pathname, "/");

	const error = useSelector((state) => state.register.error);
	useEffect(() => {
		if (error && error.status === 403) {
			setError("steps", {
				type: "manual",
				message: "Необходимо ввести код подтверждения",
			});
		}
		if (error && error.status === 404) {
			setError("steps", {
				type: "manual",
				message: "Вам следует перейти к первому шагу регистрации",
			});
		}
	}, [error, setError]);

	const handleRegistration = async (data) => {
		const response = await dispatch(
			registerAsync({
				password: data.password,
				registrationId: registrationId,
			})
		);
		if (!response.error) {
			navigate("/auth");
		}
	};

	return (
		<form onSubmit={handleSubmit(handleRegistration)}>
			<InputField
				label="Пароль"
				type="password"
				name="password"
				error={errors.password}
				placeholder="Пароль"
				rules={passwordRules}
				trigger={trigger}
				control={control}
				required={true}
			/>
			<InputField
				label="Повторите пароль"
				type="password"
				name="repeatPassword"
				error={errors.repeatPassword}
				placeholder="Повторите пароль"
				rules={{
					required: "Повторите пароль",
					validate: (value) =>
						value === watch("password") || "Пароли не совпадают",
				}}
				trigger={trigger}
				control={control}
				required={true}
			/>
			<ErrorMessage error={errors.steps} />

			<button className={styles["registration__button"]} type="submit">
				Зарегистрироваться
			</button>
		</form>
	);
};

export default RegistrationEnd;
