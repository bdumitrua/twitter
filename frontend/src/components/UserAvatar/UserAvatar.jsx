import React from "react";
import { Link } from "react-router-dom";

import styles from "../../assets/styles/components/UserAvatar/UserAvatar.module.scss";

const UserAvatar = ({ userPhoto, link }) => {
	return (
		<Link to={link} className={styles["user-avatar__link"]}>
			<img className={styles["user-avatar"]} src={userPhoto} alt="" />
		</Link>
	);
};

export default UserAvatar;
