/* eslint-disable @typescript-eslint/no-explicit-any */
import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { formatDate } from "@/utils/functions/formatDate";
import { getLastEntry } from "@/utils/functions/getLastEntry";
import { FieldValues, useForm } from "react-hook-form";
import {
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
	const registrationId: number = getLastEntry(location.pathname, "/");

	const queryParams: URLSearchParams = new URLSearchParams(location.search);
	const name: string | null = queryParams.get("name");
	const email: string | null = queryParams.get("email");
	const birthDate: string | null = queryParams.get("birth_date");

	const handleConfirmRegistration: () => void = () => {
		navigate(`/registration/code/${registrationId}`);
	};

	return (
		<form
			className={styles["registration__form"]}
			onSubmit={handleSubmit(handleConfirmRegistration)}
		>
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
				name="birth_date"
				defaultValue={formatDate(birthDate)}
				trigger={trigger}
				control={control}
				disabled={true}
			/>
			<button className={styles["registration__button"]} type="submit">
				Далее
			</button>
		</form>
	);
};

export default RegistrationConfirmation;
