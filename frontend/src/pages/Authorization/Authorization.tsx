import cancelReg from "@/assets/images/Tweet/cancelReg.svg";
import styles from "@/assets/styles/pages/Auth/Authorization.scss";
import { loginAsync, setLoggedIn } from "@/redux/slices/auth.slice";
import { getMeAsync } from "@/redux/slices/user.slice";
import { AppDispatch, RootState } from "@/redux/store";
import { LoginPayload } from "@/types/redux/auth";
import { useState } from "react";
import { SubmitHandler, useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { Link, NavigateFunction, useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { emailRules, passwordRules } from "../../utils/inputRules";

const Authorization = () => {
	const {
		control,
		handleSubmit,
		trigger,
		formState: { errors },
	} = useForm<LoginPayload>();
	const navigate: NavigateFunction = useNavigate();
	const dispatch = useDispatch<AppDispatch>();
	const loading: boolean = useSelector(
		(state: RootState) => state.auth.loading
	);
	const [generalError, setGeneralError] = useState<string | undefined>("");

	const handleLogin: SubmitHandler<LoginPayload> = async (data) => {
		const response = await dispatch(
			loginAsync({ email: data.email, password: data.password })
		);
		if (response.meta.requestStatus === "rejected") {
			setGeneralError("Неверная почта или пароль!");
		} else {
			dispatch(getMeAsync());
			dispatch(setLoggedIn(true));
			navigate("/feed");
		}
	};

	return (
		<div className={styles["auth"]}>
			<header className={styles["auth__header"]}>
				<Link
					to="/welcome"
					style={{ display: "flex", alignItems: "center" }}
				>
					<img src={cancelReg} alt="Cancel" />
				</Link>
			</header>
			<form
				className={styles["auth__form"]}
				onSubmit={handleSubmit(handleLogin)}
			>
				<h2 className={styles["auth__title"]}>Введите пароль</h2>

				<InputField
					label="Адрес электронной почты"
					type="email"
					name="email"
					error={errors?.email?.message?.toString()}
					trigger={trigger}
					control={control}
					required={true}
					rules={emailRules}
				/>
				<InputField
					label="Пароль"
					type="password"
					error={errors?.password?.message?.toString()}
					control={control}
					required={true}
					trigger={trigger}
					rules={passwordRules}
				/>

				<button className={styles["auth__button"]} type="submit">
					{loading ? "Вход..." : "Войти"}
				</button>
			</form>
		</div>
	);
};

export default Authorization;
