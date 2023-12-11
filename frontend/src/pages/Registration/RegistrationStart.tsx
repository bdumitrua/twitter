import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { AppDispatch, RootState } from "@/redux/store";
import { ErrorMessages } from "@/types/api";
import { RegisterError, RegisterStartPayload } from "@/types/redux/register";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { NavigateFunction, useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { startRegisterAsync } from "../../redux/slices/register.slice";
import { emailRules, nameRules } from "../../utils/inputRules";

const RegistrationStart: React.FC = () => {
	const {
		control,
		trigger,
		setError,
		handleSubmit,
		formState: { errors },
	} = useForm<RegisterStartPayload>();
	const navigate: NavigateFunction = useNavigate();
	const dispatch = useDispatch<AppDispatch>();

	const handleStartRegistration: (
		data: RegisterStartPayload
	) => Promise<void> = async (data) => {
		const response = await dispatch(
			startRegisterAsync({
				name: data.name,
				email: data.email,
				birthDate: data.birthDate,
			})
		);
		if (response.meta.requestStatus === "fulfilled") {
			const queryParams = new URLSearchParams({
				name: data.name,
				email: data.email,
				birthDate: data.birthDate,
			}).toString();
			navigate(
				`/registration/confirm/${response.payload}?${queryParams}`
			);
		}
	};

	const error: RegisterError | null = useSelector(
		(state: RootState) => state.register.error
	);

	const errorMessages: ErrorMessages = {
		422: "Данная почта уже занята",
	};
	useEffect(() => {
		if (error && errorMessages[error.status]) {
			setError("email", {
				type: "manual",
				message: errorMessages[error.status],
			});
		} else if (error && !errorMessages[error.status]) {
			setError("email", {
				type: "manual",
				message: "Неизвестная ошибка, обратитесь в тех. поддержку.",
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
				error={errors?.name?.message?.toString()}
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
				error={errors?.email?.message?.toString()}
				placeholder="Почта"
				rules={emailRules}
				trigger={trigger}
				control={control}
				required={true}
			/>
			<InputField
				label="Дата рождения"
				type="date"
				name="birthDate"
				error={errors?.birthDate?.message?.toString()}
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
