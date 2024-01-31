import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { AppDispatch, RootState } from "@/redux/store";
import { ErrorMessages } from "@/types/api";
import { RegisterError, RegisterStartPayload } from "@/types/redux/register";
import { useEffect, useState } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { Link, NavigateFunction, useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { startRegisterAsync } from "../../redux/slices/register.slice";
import { emailRules, nameRules } from "../../utils/inputRules";

import openDropdown from "@/assets/images/Pages/openDropdown.svg";
import cancelReg from "@/assets/images/Tweet/cancelReg.svg";

interface Month {
	value: string;
	label: string;
	days: number;
}

interface DayOption {
	value: string;
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
				birthDate: `${selectedYear}-${selectedMonth}-${selectedDay}`,
			})
		);
		if (response.meta.requestStatus === "fulfilled") {
			const queryParams = new URLSearchParams({
				name: data.name,
				email: data.email,
				birthDate: `${selectedYear}-${selectedMonth}-${selectedDay}`,
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

	const [selectedMonth, setSelectedMonth] = useState<string>("");
	const [selectedDay, setSelectedDay] = useState<string>("");
	const [selectedYear, setSelectedYear] = useState<string>("");

	const generateDays = (monthValue: string): DayOption[] => {
		const month = months.find((m) => m.value === monthValue);
		if (!month) {
			return [];
		}
		return Array.from({ length: month.days }, (_, index) => ({
			value:
				(index + 1).toString().length === 2
					? (index + 1).toString()
					: "0" + (index + 1),
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
				<Link
					to="/welcome"
					style={{ display: "flex", alignItems: "center" }}
				>
					<img src={cancelReg} alt="Cancel" />
				</Link>
				Шаг 1 из 4
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
						rules={emailRules}
						trigger={trigger}
						control={control}
						required={true}
					/>
				</div>

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
						<div
							className={styles["registration__select-wrapper"]}
							style={{ flex: 6 }}
						>
							<label
								htmlFor="birthMonth"
								className={styles["registration__select-label"]}
							>
								Месяц
							</label>

							<select
								name="birthMonth"
								id="birthMonth"
								onChange={(e) =>
									setSelectedMonth(e.target.value)
								}
								className={styles["registration__select"]}
							>
								<option value="" disabled>
									{""}
								</option>
								{months.map((month) => (
									<option
										key={month.value}
										value={month.value}
									>
										{month.label}
									</option>
								))}
							</select>
							<p className={styles["registration__select-value"]}>
								{selectedMonth
									? months[+selectedMonth - 1].label
									: ""}
							</p>
							<img
								src={openDropdown}
								alt="Иконка"
								className={styles["registration__dropdown"]}
							/>
						</div>

						<div
							className={styles["registration__select-wrapper"]}
							style={{ flex: 3 }}
						>
							<label
								htmlFor="birthDay"
								className={styles["registration__select-label"]}
							>
								День
							</label>

							<select
								name="birthDay"
								id="birthDay"
								onChange={(e) => setSelectedDay(e.target.value)}
								value={selectedDay}
								className={styles["registration__select"]}
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
							<p className={styles["registration__select-value"]}>
								{selectedDay}
							</p>
							<img
								src={openDropdown}
								alt="Иконка"
								className={styles["registration__dropdown"]}
							/>
						</div>

						<div
							className={styles["registration__select-wrapper"]}
							style={{ flex: 4 }}
						>
							<label
								htmlFor="birthYear"
								className={styles["registration__select-label"]}
							>
								Год
							</label>

							<select
								name="birthYear"
								id="birthYear"
								onChange={(e) =>
									setSelectedYear(e.target.value)
								}
								value={selectedYear}
								className={styles["registration__select"]}
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
							<p className={styles["registration__select-value"]}>
								{selectedYear}
							</p>
							<img
								src={openDropdown}
								alt="Иконка"
								className={styles["registration__dropdown"]}
							/>
						</div>
					</div>
				</div>
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

export default RegistrationStart;
