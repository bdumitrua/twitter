import styles from "@/assets/styles/components/UserAvatar/UserAvatarPlug.module.scss";
import { Link } from "react-router-dom";

interface UserAvatarPlugProps {
	userName: string | undefined;
	authorId: number | undefined;
}

const UserAvatarPlug: React.FC<UserAvatarPlugProps> = ({
	userName,
	authorId,
}) => {
	if (userName === undefined) {
		return null;
	} else if (authorId === undefined) {
		return null;
	}

	return (
		<Link to={`/profile/${authorId}`} className={styles["user-image-plug"]}>
			{userName[0].toUpperCase()}
		</Link>
	);
};

export default UserAvatarPlug;
