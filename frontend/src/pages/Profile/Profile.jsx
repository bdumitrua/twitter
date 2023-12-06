import React from "react";
import { Outlet } from "react-router-dom";

import styles from "../../assets/styles/pages/Profile/Profile.module.scss";
import UserAvatar from "../../components/UserAvatar/UserAvatar";

import { Link } from "react-router-dom";
import banner from "../../assets/images/Pages/Profile/banner.png";
import calendarIcon from "../../assets/images/Pages/Profile/calendarIcon.svg";
import linkIcon from "../../assets/images/Pages/Profile/linkIcon.svg";
import userPhoto from "../../assets/images/Tweet/pictureExample.jpg";

const tabs = [
	{ name: "Tweets", value: "tweets" },
	{ name: "Tweets & replies", value: "tweets-with-replies" },
	{ name: "Media", value: "media" },
	{ name: "Likes", value: "likes" },
];

const Profile = () => {
	const [activeTab, setActiveTab] = React.useState("tweets");

	const handleTabClick = (value) => {
		setActiveTab(value);
	};

	return (
		<>
			<div className={styles["account"]}>
				<div className={styles["profile"]}>
					<img
						className={styles["profile__banner"]}
						src={banner}
						alt=""
					/>
					<div className={styles["profile__bar"]}>
						<div className={styles["profile__upper"]}>
							<div className={styles["profile__avatar"]}>
								<UserAvatar
									userPhoto={userPhoto}
									link="/profile"
								/>
							</div>
							<button className={styles["profile__edit-button"]}>
								Edit profile
							</button>
						</div>
						<div className={styles["profile__user-data"]}>
							<div className={styles["profile__username"]}>
								Pixsellz
							</div>
							<div className={styles["profile__nickname"]}>
								@pixsellz
							</div>
						</div>
						<div className={styles["profile__biography"]}>
							Digital Goodies Team - Web & Mobile UI/UX
							development; Graphics; Illustrations
						</div>
						<div className={styles["profile__additional-data"]}>
							<div className={styles["profile__link-block"]}>
								<img src={linkIcon} alt="" />
								<a
									className={styles["profile__link"]}
									href="https://bebra.io"
								>
									bebra.io
								</a>
							</div>
							<div className={styles["profile__date-block"]}>
								<img src={calendarIcon} alt="" />
								<span className={styles["profile__date"]}>
									Joined September 2007
								</span>
							</div>
						</div>
						<div className={styles["profile__counters"]}>
							<div className={styles["profile__counter"]}>
								<span
									className={
										styles["profile__counter-number"]
									}
								>
									216
								</span>
								<span
									className={styles["profile__counter-text"]}
								>
									Following
								</span>
							</div>
							<div className={styles["profile__counter"]}>
								<span
									className={
										styles["profile__counter-number"]
									}
								>
									117
								</span>
								<span
									className={styles["profile__counter-text"]}
								>
									Followers
								</span>
							</div>
						</div>
					</div>
				</div>
				<div className="tabs">
					<div className={styles["tabs__row"]}>
						{tabs.map((tab) => (
							<Link
								to={tab.value}
								key={tab.value}
								className={`${styles["tabs__tab"]} ${
									activeTab === tab.value
										? styles["active"]
										: ""
								}`}
								onClick={() => handleTabClick(tab.value)}
							>
								{tab.name}
							</Link>
						))}
					</div>
				</div>
			</div>
			<Outlet />
		</>
	);
};

export default Profile;
