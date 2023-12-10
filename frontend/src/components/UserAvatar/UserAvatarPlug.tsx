import styles from "@/assets/styles/components/UserAvatar/UserAvatarPlug.module.scss";
import { RootState } from "@/redux/store";
import { User } from "@/types/redux/user";
import { useSelector } from "react-redux";

const UserAvatarPlug = () => {
	const authorizedUser: User | null = useSelector(
		(state: RootState) => state.user.authorizedUser
	);

	return (
		<div className={styles["user-image-plug"]}>
			{authorizedUser?.name[0].toUpperCase()}
		</div>
	);
};

export default UserAvatarPlug;
