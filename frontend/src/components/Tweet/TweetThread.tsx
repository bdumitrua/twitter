import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/TweetThread.module.scss";
import UserAvatar from "../UserAvatar/UserAvatar";
import UserAvatarPlug from "../UserAvatar/UserAvatarPlug";

interface ThreadProps {
	authorAvatar: string | null;
	authorName: string;
}

const TweetThread: React.FC<ThreadProps> = ({ authorAvatar, authorName }) => {
	return (
		<div className={styles["tweet__thread"]}>
			<div className={styles["tweet__thread-avatar"]}>
				{authorAvatar ? (
					<UserAvatar userPhoto={authorAvatar} link="/profile" />
				) : (
					<UserAvatarPlug userName={authorName} />
				)}
			</div>
			<Link to="/tweet" className={styles["tweet__show-thread"]}>
				Show this Thread
			</Link>
		</div>
	);
};

export default TweetThread;
