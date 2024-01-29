/* eslint-disable @typescript-eslint/no-explicit-any */
import cancelReg from "@/assets/images/Tweet/cancelReg.svg";
import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { formatDate } from "@/utils/functions/formatDate";
import { getSubstring } from "@/utils/functions/getSubstring";
import { FieldValues, useForm } from "react-hook-form";
import {
	Link,
	Location,
	NavigateFunction,
	useLocation,
	useNavigate,
} from "react-router-dom";
import InputField from "../../components/InputField/InputField";

const RegistrationConfirmation = () => {
	const { control, handleSubmit, trigger } = useForm<
		FieldValues,
		any,
		undefined
	>();

	const location: Location<any> = useLocation();
	const navigate: NavigateFunction = useNavigate();
	const registrationId: number = getSubstring(location.pathname, "/");

	const queryParams: URLSearchParams = new URLSearchParams(location.search);
	const name: string | null = queryParams.get("name");
	const email: string | null = queryParams.get("email");
	const birthDate: string | null = queryParams.get("birthDate");

	const handleConfirmRegistration: () => void = () => {
		navigate(`/registration/code/${registrationId}`);
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
				Шаг 2 из 5
			</header>
			<form
				className={styles["registration__fields"]}
				onSubmit={handleSubmit(handleConfirmRegistration)}
			>
				<h2 className={styles["registration__title"]}>
					Создайте учетную запись
				</h2>
				<InputField
					label="Имя"
					type="text"
					name="name"
					defaultValue={name}
					trigger={trigger}
					control={control}
					disabled={true}
				/>
				<InputField
					label="Почта"
					type="email"
					name="email"
					defaultValue={email}
					trigger={trigger}
					control={control}
					disabled={true}
				/>
				<InputField
					label="Дата рождения"
					type="text"
					name="birthDate"
					defaultValue={formatDate(birthDate)}
					trigger={trigger}
					control={control}
					disabled={true}
				/>

				<span className={styles["registration__terms"]}>
					Регистрируюясь, вы принимаете Условиями предоставления
					услуг, Политику конфиденциальности, и Политику использования
					файлов cookie.{" "}
					<Link to="/" className={styles["registration__terms-link"]}>
						Подробнее
					</Link>
				</span>
				<button
					className={styles["registration__button-reg"]}
					type="submit"
				>
					Зарегистрироваться
				</button>
			</form>
		</div>
	);
};

export default RegistrationConfirmation;
