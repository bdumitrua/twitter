import styles from "@/assets/styles/pages/Auth/Registration.scss";
import { Outlet } from "react-router-dom";

const Registration: React.FC = () => {
	return (
		<div className={styles["registration"]}>
			<Outlet />
		</div>
	);
};

export default Registration;
