import { Link } from "react-router-dom";

import styles from "../../assets/styles/components/UserAvatar/UserAvatar.module.scss";

interface UserAvatarProps {
	userPhoto: string;
	userId: number;
}

const UserAvatar: React.FC<UserAvatarProps> = ({ userPhoto, userId }) => {
	return (
		<Link to={`/profile/${userId}`} className={styles["user-avatar__link"]}>
			<img className={styles["user-avatar"]} src={userPhoto} alt="" />
		</Link>
	);
};

export default UserAvatar;
