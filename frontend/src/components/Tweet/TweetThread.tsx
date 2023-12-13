import { Link } from "react-router-dom";
import styles from "../../assets/styles/components/Tweet/TweetThread.module.scss";
import UserAvatar from "../UserAvatar/UserAvatar";
import UserAvatarPlug from "../UserAvatar/UserAvatarPlug";

interface ThreadProps {
	authorAvatar: string | null;
	authorName: string;
	authorId: number;
}

const TweetThread: React.FC<ThreadProps> = ({
	authorAvatar,
	authorName,
	authorId,
}) => {
	return (
		<div className={styles["tweet__thread"]}>
			<div className={styles["tweet__thread-avatar"]}>
				{authorAvatar ? (
					<UserAvatar authorId={1} userPhoto={authorAvatar} />
				) : (
					<UserAvatarPlug authorId={authorId} userName={authorName} />
				)}
			</div>
			<Link to="/tweet" className={styles["tweet__show-thread"]}>
				Show this Thread
			</Link>
		</div>
	);
};

export default TweetThread;
