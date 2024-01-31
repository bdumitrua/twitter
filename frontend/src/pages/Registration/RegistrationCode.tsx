/* eslint-disable @typescript-eslint/no-explicit-any */
import cancelReg from "@/assets/images/Tweet/cancelReg.svg";
import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { AppDispatch, RootState } from "@/redux/store";
import { ErrorMessages } from "@/types/api";
import { RegisterCodePayload, RegisterError } from "@/types/redux/register";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import {
	Link,
	Location,
	NavigateFunction,
	useLocation,
	useNavigate,
} from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { codeRegisterAsync } from "../../redux/slices/register.slice";
import { getSubstring } from "../../utils/functions/getSubstring";
import { codeRules } from "../../utils/inputRules";

const RegistrationCode = () => {
	const {
		control,
		handleSubmit,
		trigger,
		setError,
		formState: { errors },
	} = useForm<RegisterCodePayload>();

	const navigate: NavigateFunction = useNavigate();
	const location: Location<any> = useLocation();
	const dispatch = useDispatch<AppDispatch>();

	const registrationId: number = getSubstring(location.pathname, "/");
	const error: RegisterError | null = useSelector(
		(state: RootState) => state.register.error
	);

	const errorMessages: ErrorMessages = {
		400: "Вы ввели некорректный код подтверждения!",
		404: "Вам следует пройти первый шаг регистрации!",
	};
	useEffect(() => {
		if (error && errorMessages[error.status]) {
			setError("code", {
				type: "manual",
				message: errorMessages[error.status],
			});
		} else if (error && !errorMessages[error.status]) {
			setError("code", {
				type: "manual",
				message: "Неизвестная ошибка, обратитесь в тех. поддержку.",
			});
		}
	}, [error]);

	const queryParams: URLSearchParams = new URLSearchParams(location.search);
	const email: string | null = queryParams.get("email");

	const handleRegistration: (
		data: RegisterCodePayload
	) => Promise<void> = async (data) => {
		const response = await dispatch(
			codeRegisterAsync({
				code: data.code,
				registrationId: registrationId,
			})
		);
		if (response.meta.requestStatus === "fulfilled") {
			navigate(`/registration/end/${registrationId}`);
		}
	};

	return (
		<div className={styles["registration__page-container"]}>
			<header className={styles["registration__header"]}>
				<Link
					to="/welcome"
					style={{ display: "flex", alignItems: "center" }}
				>
					<img src={cancelReg} alt="Cancel" />
				</Link>
				Шаг 3 из 5
			</header>
			<form
				className={styles["registration__form"]}
				onSubmit={handleSubmit(handleRegistration)}
			>
				<h2 className={styles["registration__title"]}>
					Мы отправили вам код
				</h2>
				<span>
					Введите код в расположенном ниже поле для подтверждения{" "}
					{email}
				</span>
				<InputField
					style={{ marginTop: "12px" }}
					label="Проверочный код"
					type="text"
					name="code"
					error={errors?.code?.message?.toString()}
					rules={codeRules}
					maxLength={5}
					trigger={trigger}
					control={control}
					required={true}
				/>
				<Link to="/" className={styles["registration__no-code"]}>
					Не получили сообщение электронной почты?
				</Link>
				<button
					className={styles["registration__button-next"]}
					type="submit"
				>
					Далее
				</button>
			</form>
		</div>
	);
};

export default RegistrationCode;
