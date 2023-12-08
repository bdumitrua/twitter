import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { ErrorMessage } from "@/components/ErrorMessage/ErrorMessage";
import { registerAsync } from "@/redux/slices/register.slice";
import { AppDispatch, RootState } from "@/redux/store";
import { RegisterEndPayload } from "@/types/redux/register";
import { useEffect, useState } from "react";
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
		trigger,
		formState: { errors },
	} = useForm<RegisterEndPayload>();
	const [generalError, setGeneralError] = useState<string | null>(null);

	const navigate = useNavigate();
	const dispatch = useDispatch<AppDispatch>();

	const registrationId = getLastEntry(location.pathname, "/");

	const error = useSelector((state: RootState) => state.register.error);
	useEffect(() => {
		if (error && error.status === 403) {
			setGeneralError("Необходимо ввести код подтверждения");
		} else if (error && error.status === 404) {
			setGeneralError("Вам следует перейти к первому шагу регистрации");
		} else {
			setGeneralError(null);
		}
	}, [error]);

	const handleRegistration = async (data: RegisterEndPayload) => {
		const response = await dispatch(
			registerAsync({
				password: data.password,
				registrationId: registrationId,
			})
		);
		if (response.meta.requestStatus === "rejected") {
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
				error={errors?.repeatPassword?.message?.toString()}
				placeholder="Повторите пароль"
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
