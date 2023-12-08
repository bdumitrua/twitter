import styles from "@/assets/styles/pages/Auth/Authorization.scss";
import { loginAsync, setLoggedIn } from "@/redux/slices/auth.slice";
import { AppDispatch, RootState } from "@/redux/store";
import { LoginPayload } from "@/types/redux/auth";
import { useState } from "react";
import { SubmitHandler, useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { Link, useNavigate } from "react-router-dom";
import { ErrorMessage } from "../../components/ErrorMessage/ErrorMessage";
import InputField from "../../components/InputField/InputField";
import { emailRules, passwordRules } from "../../utils/inputRules";

const Authorization = () => {
	const {
		control,
		handleSubmit,
		trigger,
		formState: { errors },
	} = useForm<LoginPayload>();
	const navigate = useNavigate();
	const dispatch = useDispatch<AppDispatch>();
	const loading = useSelector((state: RootState) => state.auth.loading);
	const [generalError, setGeneralError] = useState<string | undefined>("");

	const handleLogin: SubmitHandler<LoginPayload> = async (data) => {
		const response = await dispatch(
			loginAsync({ email: data.email, password: data.password })
		);
		if (response.meta.requestStatus === "rejected") {
			setGeneralError("Неверная почта или пароль!");
		} else {
			dispatch(setLoggedIn(true));
			navigate("/feed");
		}
	};

	return (
		<div className={styles["auth"]}>
			<Link to="/registration" className={`${styles["auth__switch"]}`}>
				Регистрация
			</Link>

			<form
				className={styles["auth__form"]}
				onSubmit={handleSubmit(handleLogin)}
			>
				<InputField
					label="Почта"
					type="email"
					name="email"
					error={errors?.email?.message?.toString()}
					placeholder="Почта"
					trigger={trigger}
					control={control}
					required={true}
					rules={emailRules}
				/>
				<InputField
					label="Пароль"
					type="password"
					placeholder="Пароль"
					error={errors?.password?.message?.toString()}
					control={control}
					required={true}
					trigger={trigger}
					rules={passwordRules}
				/>

				<ErrorMessage error={generalError} />

				<button className={styles["auth__button"]} type="submit">
					{loading ? "Вход..." : "Войти"}
				</button>
			</form>
		</div>
	);
};

export default Authorization;
