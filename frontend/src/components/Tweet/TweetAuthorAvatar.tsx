import styles from "../../assets/styles/components/Tweet/Tweet.module.scss";
import UserAvatar from "../UserAvatar/UserAvatar";
import UserAvatarPlug from "../UserAvatar/UserAvatarPlug";

interface TweetAuthorAvatarProps {
	authorAvatar: string | null;
	authorName: string;
	haveThread: boolean;
}

const TweetAuthorAvatar: React.FC<TweetAuthorAvatarProps> = ({
	authorAvatar,
	authorName,
	haveThread,
}) => {
	return (
		<div className={styles["tweet__image"]}>
			<div className={styles["tweet__author-image"]}>
				{authorAvatar ? (
					<UserAvatar userPhoto={authorAvatar} link="/profile" />
				) : (
					<UserAvatarPlug userName={authorName} />
				)}
			</div>
			{haveThread && <div className={styles["tweet__line"]}></div>}
		</div>
	);
};

export default TweetAuthorAvatar;
