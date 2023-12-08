import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { AppDispatch, RootState } from "@/redux/store";
import { RegisterStartPayload } from "@/types/redux/register";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { useNavigate } from "react-router-dom";
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
	const navigate = useNavigate();
	const dispatch = useDispatch<AppDispatch>();

	const handleStartRegistration = async (data: RegisterStartPayload) => {
		console.log(data);
		const response = await dispatch(
			startRegisterAsync({
				name: data.name,
				email: data.email,
				birth_date: data.birth_date,
			})
		);
		if (response.meta.requestStatus === "rejected") {
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

	const error = useSelector((state: RootState) => state.register.error);
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
				name="birth_date"
				error={errors?.birth_date?.message?.toString()}
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
