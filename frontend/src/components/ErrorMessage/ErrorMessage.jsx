import styles from "@/assets/styles/pages/Auth/Registration.scss";

export const ErrorMessage = ({ error }) => {
	if (!error) return null;

	return (
		<p className={styles["registration__error-register"]}>
			{error.message}
		</p>
	);
};
