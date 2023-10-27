import React from "react";
import styles from "../../assets/styles/pages/Home.module.scss";
import Header from "../../components/Header/Header";
import Tweet from "../../components/Tweet/Tweet";

const Home = () => {
    return (
        <div className={styles["home__wrapper"]}>
            <Header />
            <Tweet />
        </div>
    );
};

export default Home;
