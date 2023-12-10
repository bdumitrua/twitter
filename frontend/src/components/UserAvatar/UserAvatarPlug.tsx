import styles from "@/assets/styles/components/UserAvatar/UserAvatarPlug.module.scss";
import { RootState } from "@/redux/store";
import { useSelector } from "react-redux";

const UserAvatarPlug = () => {
	const user = useSelector((state: RootState) => state.user.user);

	return (
		<div className={styles["user-image-plug"]}>
			{user?.name[0].toUpperCase()}
		</div>
	);
};

export default UserAvatarPlug;
