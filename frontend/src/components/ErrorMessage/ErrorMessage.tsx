import styles from "@/assets/styles/pages/Auth/Registration.scss";

interface ErrorMessageProps {
	error?: string;
}

export const ErrorMessage: React.FC<ErrorMessageProps> = ({ error }) => {
	console.log(error);
	if (!error) return null;

	return <p className={styles["registration__error-register"]}>{error}</p>;
};
