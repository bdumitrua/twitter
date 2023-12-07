import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { AppDispatch, RootState } from "@/redux/store";
import { RegisterCodePayload } from "@/types/redux/register";
import { useEffect } from "react";
import { useForm } from "react-hook-form";
import { useDispatch, useSelector } from "react-redux";
import { useLocation, useNavigate } from "react-router-dom";
import InputField from "../../components/InputField/InputField";
import { codeRegisterAsync } from "../../redux/slices/register.slice";
import { getLastEntry } from "../../utils/functions/getLastEntry";
import { codeRules } from "../../utils/inputRules";

const RegistrationCode = () => {
	const {
		control,
		handleSubmit,
		trigger,
		setError,
		formState: { errors },
	} = useForm<RegisterCodePayload>();

	const navigate = useNavigate();
	const location = useLocation();
	const dispatch = useDispatch<AppDispatch>();

	const registrationId = getLastEntry(location.pathname, "/");
	const error = useSelector((state: RootState) => state.register.error);

	useEffect(() => {
		if (error && error.status === 404) {
			setError("code", {
				type: "manual",
				message: "Вам следует пройти первый шаг регистрации!",
			});
		}
		if (error && error.status === 403) {
			setError("code", {
				type: "manual",
				message: "Вы ввели некорректный код подтверждения!",
			});
		}
		if (error && error.status === 422) {
			setError("code", {
				type: "manual",
				message: "Код подтверждения должен состоять из 5 символов!",
			});
		}
	}, [error]);

	const handleRegistration = async (data: RegisterCodePayload) => {
		const response = await dispatch(
			codeRegisterAsync({
				code: data.code,
				registrationId: registrationId,
			})
		);
		if (response.meta.requestStatus === "rejected") {
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
				error={errors.code}
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
