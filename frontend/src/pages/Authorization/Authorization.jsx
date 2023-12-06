import styles from "@/assets/styles/pages/Auth/Authorization.scss";
import { loginAsync, setLoggedIn } from "@/redux/slices/auth.slice";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { Link, useNavigate } from "react-router-dom";
import { ErrorMessage } from "../../components/ErrorMessage/ErrorMessage";
import InputField from "../../components/InputField/InputField";
import { emailRules, passwordRules } from "../../utils/inputRules";

const Authorization = () => {
	const {
		control,
		handleSubmit,
		setError,
		trigger,
		formState: { errors },
	} = useForm();
	const navigate = useNavigate();
	const dispatch = useDispatch();
	const loading = useSelector((state) => state.auth.loading);

	const handleLogin = async (data) => {
		const response = await dispatch(
			loginAsync({ email: data.email, password: data.password })
		);
		if (response.error) {
			setError("auth", {
				type: "manual",
				message: "Неверная почта или пароль!",
			});
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
					error={errors.email}
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
					error={errors.password}
					control={control}
					required={true}
					trigger={trigger}
					rules={passwordRules}
				/>

				<ErrorMessage error={errors.auth} />

				<button className={styles["auth__button"]} type="submit">
					{loading ? "Вход..." : "Войти"}
				</button>
			</form>
		</div>
	);
};

export default Authorization;
