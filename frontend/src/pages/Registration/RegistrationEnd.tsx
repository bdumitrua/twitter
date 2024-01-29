import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { ErrorMessage } from "@/components/ErrorMessage/ErrorMessage";
import { registerAsync } from "@/redux/slices/register.slice";
import { AppDispatch, RootState } from "@/redux/store";
import { ErrorMessages } from "@/types/api";
import { RegisterEndPayload, RegisterError } from "@/types/redux/register";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { NavigateFunction, useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { getSubstring } from "../../utils/functions/getSubstring";
import { passwordRules } from "../../utils/inputRules";

const RegistrationEnd = () => {
	const {
		control,
		handleSubmit,
		watch,
		trigger,
		formState: { errors },
	} = useForm<RegisterEndPayload>();
	const [generalError, setGeneralError] = useState<string | null>(null);

	const navigate: NavigateFunction = useNavigate();
	const dispatch = useDispatch<AppDispatch>();

	const registrationId: number = getSubstring(location.pathname, "/");

	const error: RegisterError | null = useSelector(
		(state: RootState) => state.register.error
	);
	const errorMessages: ErrorMessages = {
		403: "Необходимо ввести код подтверждения!",
		404: "Вам следует перейти к первому шагу регистрации!",
	};
	useEffect(() => {
		if (error) {
			setGeneralError(errorMessages[error.status] ?? null);
		}
	}, [error]);

	const handleRegistration: (
		data: RegisterEndPayload
	) => Promise<void> = async (data) => {
		const response = await dispatch(
			registerAsync({
				password: data.password,
				registrationId: registrationId,
			})
		);
		if (response.meta.requestStatus === "fulfilled") {
			navigate("/auth");
		}
	};

	return (
		<form onSubmit={handleSubmit(handleRegistration)}>
			<InputField
				label="Пароль"
				type="password"
				name="password"
				error={errors?.password?.message?.toString()}
				rules={passwordRules}
				trigger={trigger}
				control={control}
				required={true}
			/>
			<InputField
				label="Повторите пароль"
				type="password"
				name="repeatPassword"
				error={errors?.repeatPassword?.message?.toString()}
				rules={{
					required: "Повторите пароль",
					validate: (value: string) =>
						value === watch("password") || "Пароли не совпадают",
				}}
				trigger={trigger}
				control={control}
				required={true}
			/>
			{generalError && <ErrorMessage error={generalError} />}

			<button className={styles["registration__button"]} type="submit">
				Зарегистрироваться
			</button>
		</form>
	);
};

export default RegistrationEnd;
