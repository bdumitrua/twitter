/* eslint-disable @typescript-eslint/no-explicit-any */
import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { AppDispatch, RootState } from "@/redux/store";
import { RegisterCodePayload, RegisterError } from "@/types/redux/register";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import {
	Location,
	NavigateFunction,
	useLocation,
	useNavigate,
} from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { codeRegisterAsync } from "../../redux/slices/register.slice";
import { getLastEntry } from "../../utils/functions/getLastEntry";
import { codeRules } from "../../utils/inputRules";

interface ErrorMessages {
	[key: number]: string;
}

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

	const registrationId: number = getLastEntry(location.pathname, "/");
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
		<form
			className={styles["registration__form"]}
			onSubmit={handleSubmit(handleRegistration)}
		>
			<InputField
				label="Код подтверждения"
				type="text"
				name="code"
				error={errors?.code?.message?.toString()}
				placeholder="Код подтверждения"
				rules={codeRules}
				maxLength={5}
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

export default RegistrationCode;
