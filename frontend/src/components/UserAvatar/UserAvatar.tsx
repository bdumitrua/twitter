import { Link } from "react-router-dom";

import styles from "../../assets/styles/components/UserAvatar/UserAvatar.module.scss";

interface UserAvatarProps {
	userPhoto: string;
	authorId: number;
}

const UserAvatar: React.FC<UserAvatarProps> = ({ userPhoto, authorId }) => {
	return (
		<Link
			to={`/profile/${authorId}`}
			className={styles["user-avatar__link"]}
		>
			<img className={styles["user-avatar"]} src={userPhoto} alt="" />
		</Link>
	);
};

export default UserAvatar;
