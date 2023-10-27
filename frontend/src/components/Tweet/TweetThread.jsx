import styles from "../../assets/styles/components/Tweet/TweetThread.module.scss";
import userPhoto from "../../assets/images/Tweet/userPhoto.svg";

const TweetThread = () => {
    return (
        <div className={styles["tweet__thread"]}>
            <img
                className={styles["tweet__small-user"]}
                src={userPhoto}
                alt=""
            />
            <a className={styles["tweet__show-thread"]} href=" ">
                Show this Thread
            </a>
        </div>
    );
};

export default TweetThread;
