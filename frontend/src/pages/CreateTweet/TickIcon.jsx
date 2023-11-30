import React from "react";
import { motion } from 'framer-motion';

import styles from "../../assets/styles/pages/CreateTweet/TickIcon.module.scss";

const CircleProgress = ({ charCount, maxCharCount }) => {
    const baseRadius = 8.25;
    const fullRadius = 12;
    const isFull = charCount >= maxCharCount;
    const radius = isFull ? fullRadius : baseRadius;
    const circumference = 2 * Math.PI * radius;

    const offset = isFull ? 0 : circumference - (charCount / maxCharCount) * circumference;
    const progressClass = isFull ? `${styles["progress__full"]}` : `${styles["progress"]}`;

    const overage = charCount >= maxCharCount ? charCount - maxCharCount + 1 : 0;
    const overageText = overage > 0 ? `-${overage}` : '';

    return (
        <svg width="50" height="50" viewBox="0 0 50 50">
            <motion.circle
                className={styles["base-circle"]}
                initial={{ r: baseRadius }}
                animate={{ r: radius }}
                transition={{ duration: 0.5 }}
                cx="25"
                cy="25"
                fill="none"
                strokeWidth="1.5"
            />
            <motion.circle
                className={progressClass}
                initial={{ r: baseRadius }}
                animate={{ r: radius }}
                transition={{ duration: 0.5 }}
                cx="25"
                cy="25"
                fill="none"
                strokeWidth="1.5"
                strokeDasharray={circumference}
                strokeDashoffset={offset}
                transform="rotate(-90 25 25)"
            />
            {overage > 0 && (
                <text
                    x="50%"
                    y="50%"
                    dy=".3em"
                    textAnchor="middle"
                    className={styles['progress__overage-text']}
                >
                    {overageText}
                </text>
            )}
        </svg>
    );
};

export default CircleProgress;
