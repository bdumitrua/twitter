import React from "react";

import styles from "../../assets/styles/pages/TweetPage/TweetPage.module.scss";
import Header from "../../components/Header/Header";
import TweetAdditional from "../../components/Tweet/TweetAdditional";
import Tweet from "../../components/Tweet/Tweet";
import Footer from "../../components/Footer/Footer";

import comment from "../../assets/images/Tweet/comment.svg";
import makeRepost from "../../assets/images/Tweet/makeRepost.svg";
import retweet from "../../assets/images/Tweet/retweet.svg";
import retweetWithComment from "../../assets/images/Tweet/retweetWithComment.svg";
import unpaintedLike from "../../assets/images/Tweet/unpaintedLike.svg";
import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import pictureExample from "../../assets/images/Tweet/pictureExample.jpg";

const RetweetModal = ({ onClose, showModal }) => (
	<div className={`${styles["modal"]} ${showModal && styles["modal__open"]}`}>
		<div className={styles["modal__wrapper"]}>
			<button className={styles["modal__button"]} onClick={onClose}>
				<img src={retweet} alt="" />
				<span>Retweet</span>
			</button>
			<button className={styles["modal__button"]} onClick={onClose}>
				<img src={retweetWithComment} alt="" />
				<span>Retweet with comment</span>
			</button>
			<button className={styles["modal__cancel"]} onClick={onClose}>
				Cancel
			</button>
		</div>
	</div>
);

const TweetPage = () => {
	const [showModal, setShowModal] = React.useState(false);

	return (
		<>
			<Header />
			<div className={styles["wrapper"]}>
				<div className={styles["tweet"]}>
					<div className={styles["tweet__wrapper"]}>
						<div className={styles["tweet__content"]}>
							<TweetAdditional />
							<div className={styles["tweet__user-info"]}>
								<div className={styles["tweet__image"]}>
									<img
										className={styles["tweet__user-avatar"]}
										src={userPhoto}
										alt=""
									/>
								</div>
								<div className={styles["tweet__names"]}>
									<span className={styles["tweet__username"]}>
										Martha Craig
									</span>
									<span className={styles["tweet__nickname"]}>
										@craig_love
									</span>
								</div>
							</div>
							<div className={styles["tweet__tweet-body"]}>
								<span className={styles["tweet__text"]}>
									~~ hiring for a UX Lead in Sydney - who
									should I talk to?
								</span>
								<div
									className={styles["tweet__picture-wrapper"]}
								>
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
							</div>
							<div className={styles["tweet__time-date"]}>
								<div className={styles["tweet__time"]}>
									09:28
								</div>
								<div className={styles["tweet__date"]}>
									· 2/21/20
								</div>
							</div>
							<div className={styles["tweet__counters"]}>
								<div className={styles["tweet__retweets"]}>
									<div className={styles["tweet__counter"]}>
										6
									</div>
									<span>Retweets</span>
								</div>
								<div className={styles["tweet__likes"]}>
									<div className={styles["tweet__counter"]}>
										15
									</div>
									<span>Likes</span>
								</div>
							</div>
							<div className={styles["tweet__buttons"]}>
								<div className={styles["tweet__button"]}>
									<img src={comment} alt="" />
								</div>
								<div
									className={styles["tweet__button"]}
									onClick={() => setShowModal(true)}
								>
									<img src={retweet} alt="" />
								</div>
								<div className={styles["tweet__button"]}>
									<img src={unpaintedLike} alt="" />
								</div>
								<div className={styles["tweet__button"]}>
									<img src={makeRepost} alt="" />
								</div>
							</div>
						</div>
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
