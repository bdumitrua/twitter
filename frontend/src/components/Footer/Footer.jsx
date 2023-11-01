import styles from "../../assets/styles/components/Footer.module.scss";
import homeIcon from "../../assets/images/Footer/homeIcon.svg";
import searchIcon from "../../assets/images/Footer/searchIcon.svg";
import notificationsIcon from "../../assets/images/Footer/notificationsIcon.svg";
import messageIcon from "../../assets/images/Footer/messageIcon.svg";

const Footer = () => {
    return (
        <div className={styles["footer"]}>
            <a href=" ">
                <img src={homeIcon} alt="" />
            </a>
            <a href=" ">
                <img src={searchIcon} alt="" />
            </a>
            <a href=" ">
                <img src={notificationsIcon} alt="" />
            </a>
            <a href=" ">
                <img src={messageIcon} alt="" />
            </a>
        </div>
    );
};
export default Footer;
