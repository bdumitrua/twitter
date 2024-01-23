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

import cancelReg from "@/assets/images/Tweet/cancelReg.svg";

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
		<div className={styles["registration__page-container"]}>
			<header className={styles["registration__header"]}>
				<img src={cancelReg} alt="Cancel" />
				Шаг 1 из 5
			</header>
			<form
				onSubmit={handleSubmit(handleStartRegistration)}
				className={styles["registration__form"]}
			>
				<h2 className={styles["registration__title"]}>
					Создайте учетную запись
				</h2>
				<div className={styles["registration__fields"]}>
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
						label="Адрес электронной почты"
						type="email"
						name="email"
						error={errors?.email?.message?.toString()}
						placeholder="Почта"
						rules={emailRules}
						trigger={trigger}
						control={control}
						required={true}
					/>
				</div>
				{}
				<button className={styles["registration__changer"]}>
					Использовать телефон
				</button>
				<div className={styles["registration__birth-date"]}>
					<h6 className={styles["registration__birth-date-title"]}>
						Дата рождения
					</h6>
					<p className={styles["registration__birth-date-text"]}>
						Эта информация не будет общедоступной. Подтвердите свой
						возраст даже если эта учетная запись предназначена для
						компании, домашнего животного и т.д.
					</p>
					<div className={styles["registration__birth-date-fields"]}>
						<InputField
							style={{ flex: 6 }}
							label="Месяц"
							type="text"
							name="birthMonth"
							error={errors?.birthDate?.message?.toString()}
							rules={{
								required: "Дата обязательна к заполнению.",
							}}
							trigger={trigger}
							control={control}
							required={true}
						/>
						<InputField
							style={{ flex: 3 }}
							label="День"
							type="text"
							name="birthDay"
							trigger={trigger}
							control={control}
							required={true}
						/>
						<InputField
							style={{ flex: 4 }}
							label="Год"
							type="text"
							name="birthYear"
							trigger={trigger}
							control={control}
							required={true}
						/>
					</div>
				</div>
				<button
					className={styles["registration__button"]}
					type="submit"
				>
					Зарегистрироваться
				</button>
			</form>
		</div>
	);
};

export default RegistrationStart;
