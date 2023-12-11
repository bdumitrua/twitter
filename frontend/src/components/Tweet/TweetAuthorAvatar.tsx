import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import UserAvatar from "../UserAvatar/UserAvatar";
import UserAvatarPlug from "../UserAvatar/UserAvatarPlug";

interface TweetAuthorAvatarProps {
	authorAvatar: string | null;
	authorName: string;
}

const TweetAuthorAvatar: React.FC<TweetAuthorAvatarProps> = ({
	authorAvatar,
	authorName,
}) => {
	return (
		<div className={styles["tweet__author-image"]}>
			{authorAvatar ? (
				<UserAvatar userPhoto={authorAvatar} link="/profile" />
			) : (
				<UserAvatarPlug userName={authorName} />
			)}
		</div>
	);
};

export default TweetAuthorAvatar;
