import { useState } from "react";

import styles from "../../assets/styles/pages/TweetPage/TweetPage.module.scss";
import Tweet from "../../components/Tweet/Tweet";
import TweetAdditional from "../../components/Tweet/TweetAdditional";
import UserAvatar from "../../components/UserAvatar/UserAvatar";
import { parseHashtags } from "../../utils/functions/parseHashtags";
import RetweetModal from "./RetweetModal";

import arrow from "../../assets/images/Tweet/arrow.svg";
import comment from "../../assets/images/Tweet/comment.svg";
import makeRepost from "../../assets/images/Tweet/makeRepost.svg";
import pictureExample from "../../assets/images/Tweet/pictureExample.jpg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import shadedLike from "../../assets/images/Tweet/shadedLike.svg";
import unpaintedLike from "../../assets/images/Tweet/unpaintedLike.svg";
import userPhoto from "../../assets/images/Tweet/userPhoto.svg";

const TweetPage: React.FC = () => {
	const [showModal, setShowModal] = useState<boolean>(false);
	const [likeSrc, setLikeSrc] = useState<string>(unpaintedLike);
	const [likeActive, setLikeActive] = useState<boolean>(false);

	const onClickLike: () => void = () => {
		if (likeSrc === unpaintedLike) {
			setLikeSrc(shadedLike);
			setLikeActive(true);
		} else {
			setLikeSrc(unpaintedLike);
			setLikeActive(false);
		}
	};
	return (
		<>
			<div className={styles["tweet"]}>
				<TweetAdditional />
				<div className={styles["tweet__upper"]}>
					<div className={styles["tweet__user"]}>
						<UserAvatar userPhoto={userPhoto} link="/profile" />
						<div className={styles["tweet__names"]}>
							<span className={styles["tweet__username"]}>
								Martha Craig
							</span>
							<span className={styles["tweet__nickname"]}>
								@craig_love
							</span>
						</div>
					</div>
					<button className={styles["tweet__arrow"]}>
						<img src={arrow} alt="" />
					</button>
				</div>
				<div className={styles["tweet__text"]}>
					{parseHashtags(
						" ~~ hiring for a UX Lead in Sydney - who should I talk to? #TellMeAboutYou",
						"tweet-page"
					)}
				</div>
				<div className={styles["tweet__picture-wrapper"]}>
					<img
						className={styles["tweet__picture"]}
						src={pictureExample}
						alt=""
					/>
					<img
						className={styles["tweet__picture"]}
						src={pictureExample}
						alt=""
					/>
					<img
						className={styles["tweet__picture"]}
						src={pictureExample}
						alt=""
					/>
				</div>
				<div className={styles["tweet__time-date"]}>
					<div className={styles["tweet__time"]}>09:28</div>
					<div className={styles["tweet__date"]}>· 2/21/20</div>
				</div>
				<div className={styles["tweet__counters"]}>
					<div className={styles["tweet__retweets"]}>
						<div className={styles["tweet__counter"]}>6</div>
						<span>Retweets</span>
					</div>
					<div className={styles["tweet__likes"]}>
						<div className={styles["tweet__counter"]}>15</div>
						<span>Likes</span>
					</div>
				</div>
				<div className={styles["tweet__actions"]}>
					<div className={styles["tweet__action"]}>
						<img src={comment} alt="" />
					</div>
					<div
						className={styles["tweet__action"]}
						onClick={() => setShowModal(true)}
					>
						<img src={retweet} alt="" />
					</div>
					<button
						className={styles["tweet__action"]}
						onClick={() => {
							onClickLike();
						}}
					>
						<img
							className={`${styles["tweet__like-icon"]} ${
								likeActive ? styles["tweet__animate-like"] : ""
							}`}
							src={likeSrc}
							alt=""
						/>
					</button>
					<div className={styles["tweet__action"]}>
						<img src={makeRepost} alt="" />
					</div>
				</div>
			</div>
			<Tweet />
			<Tweet />
			<Tweet />
			<Tweet />

			<RetweetModal
				onClose={() => setShowModal(false)}
				showModal={showModal}
			/>
		</>
	);
};

export default TweetPage;
