import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import UserAvatar from "../UserAvatar/UserAvatar";
import UserAvatarPlug from "../UserAvatar/UserAvatarPlug";

interface TweetAuthorAvatarProps {
	authorAvatar: string | null;
	authorName: string;
	authorId: number;
}

const TweetAuthorAvatar: React.FC<TweetAuthorAvatarProps> = ({
	authorAvatar,
	authorName,
	authorId,
}) => {
	return (
		<div className={styles["tweet__author-image"]}>
			{authorAvatar ? (
				<UserAvatar userPhoto={authorAvatar} userId={authorId} />
			) : (
				<UserAvatarPlug authorId={authorId} userName={authorName} />
			)}
		</div>
	);
};

export default TweetAuthorAvatar;
