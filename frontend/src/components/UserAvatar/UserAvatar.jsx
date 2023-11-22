import React from "react";
import { Link } from "react-router-dom";

import userPhoto from "../../assets/images/Tweet/userPhoto.svg";
import styles from "../../assets/styles/components/UserAvatar/UserAvatar.module.scss";

const UserAvatar = () => {
	return (
		<Link to="/profile" className={styles["user-avatar__link"]}>
			<img className={styles["user-avatar"]} src={userPhoto} alt="" />
		</Link>
	);
};

export default UserAvatar;
