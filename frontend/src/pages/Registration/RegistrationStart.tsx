import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { AppDispatch, RootState } from "@/redux/store";
import { ErrorMessages } from "@/types/api";
import { RegisterError, RegisterStartPayload } from "@/types/redux/register";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { NavigateFunction, useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { startRegisterAsync } from "../../redux/slices/register.slice";
import { emailRules, nameRules } from "../../utils/inputRules";

import cancelReg from "@/assets/images/Tweet/cancelReg.svg";

interface Month {
	value: string;
	label: string;
	days: number;
}

interface DayOption {
	value: number;
	label: number;
}

const currentYear = new Date().getFullYear();
const years = Array.from({ length: 100 }, (_, index) => currentYear - index);

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
				birthDate: `${data.birthYear}-${data.birthDay}-${data.birthMonth}`,
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

	const months: Month[] = [
		{ value: "00", label: "", days: 31 },
		{ value: "01", label: "Январь", days: 31 },
		{ value: "02", label: "Февраль", days: 29 },
		{ value: "03", label: "Март", days: 31 },
		{ value: "04", label: "Апрель", days: 30 },
		{ value: "05", label: "Май", days: 31 },
		{ value: "06", label: "Июнь", days: 30 },
		{ value: "07", label: "Июль", days: 31 },
		{ value: "08", label: "Август", days: 31 },
		{ value: "09", label: "Сентябрь", days: 30 },
		{ value: "10", label: "Октябрь", days: 31 },
		{ value: "11", label: "Ноябрь", days: 30 },
		{ value: "12", label: "Декабрь", days: 31 },
	];

	const [selectedMonth, setSelectedMonth] = useState<string>("00");
	const [selectedDay, setSelectedDay] = useState<string>("");
	const [selectedYear, setSelectedYear] = useState<string>("");

	const generateDays = (monthValue: string): DayOption[] => {
		const month = months.find((m) => m.value === monthValue);
		if (!month) {
			return [];
		}
		return Array.from({ length: month.days }, (_, index) => ({
			value: index + 1,
			label: index + 1,
		}));
	};

	useEffect(() => {
		setSelectedDay("");
	}, [selectedMonth]);

	const dayOptions: DayOption[] = selectedMonth
		? generateDays(selectedMonth)
		: [];

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
						<select
							name="birthMonth"
							onChange={(e) => setSelectedMonth(e.target.value)}
						>
							{months.map((month) => (
								<option key={month.value} value={month.value}>
									{month.label}
								</option>
							))}
						</select>
						<select
							name="birthDay"
							onChange={(e) => setSelectedDay(e.target.value)}
							value={selectedDay}
						>
							<option value="" disabled>
								{""}
							</option>
							{dayOptions.map((day) => (
								<option key={day.value} value={day.value}>
									{day.label}
								</option>
							))}
						</select>
						<select
							name="birthYear"
							onChange={(e) => setSelectedYear(e.target.value)}
							value={selectedYear}
						>
							<option value="" disabled>
								{""}
							</option>
							{years.map((year) => (
								<option key={year} value={year}>
									{year}
								</option>
							))}
						</select>
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
