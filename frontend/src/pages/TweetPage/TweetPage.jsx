import React from "react";

import styles from "../../assets/styles/pages/TweetPage/TweetPage.module.scss";
import Header from "../../components/Header/Header";
import TweetAdditional from "../../components/Tweet/TweetAdditional";
import Tweet from "../../components/Tweet/Tweet";
import Footer from "../../components/Footer/Footer";
import UserAvatar from "../../components/UserAvatar/UserAvatar";
import { parseHashtags } from "../../utils/functions/parseHashtags";
import RetweetModal from "./RetweetModal";

import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import comment from "../../assets/images/Tweet/comment.svg";
import makeRepost from "../../assets/images/Tweet/makeRepost.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import unpaintedLike from "../../assets/images/Tweet/unpaintedLike.svg";
import shadedLike from "../../assets/images/Tweet/shadedLike.svg";
import pictureExample from "../../assets/images/Tweet/pictureExample.jpg";
import arrow from "../../assets/images/Tweet/arrow.svg";

const TweetPage = () => {
	const [showModal, setShowModal] = React.useState(false);
	const [likeSrc, setLikeSrc] = React.useState(unpaintedLike);
	const [likeActive, setLikeActive] = React.useState(false);

	const onClickLike = () => {
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
			<Header />
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

			<Footer />
			<RetweetModal
				onClose={() => setShowModal(false)}
				showModal={showModal}
			/>
		</>
	);
};

export default TweetPage;
