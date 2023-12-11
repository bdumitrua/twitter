import styles from "@/assets/styles/components/UserAvatar/UserAvatarPlug.module.scss";

interface UserAvatarPlugProps {
	userName: string | undefined;
}

const UserAvatarPlug: React.FC<UserAvatarPlugProps> = ({ userName }) => {
	if (userName === undefined) {
		return null;
	}

	return (
		<div className={styles["user-image-plug"]}>
			{userName[0].toUpperCase()}
		</div>
	);
};

export default UserAvatarPlug;
