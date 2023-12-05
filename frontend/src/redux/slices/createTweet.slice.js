import { createSlice } from "@reduxjs/toolkit";

const groupsList = ["Group 1", "Group 2", "Group 3"];

const createTweetSlice = createSlice({
	name: "createTweet",
	initialState: {
		tweetBodies: [],
		groupsList: groupsList,
		group: null,
		currentId: 0,
	},
	reducers: {
		addTweetBody: (state, action) => {
			const newBody = {
				id: state.currentId + 1,
				...action.payload,
				charCount: 0,
			};
			state.tweetBodies.push(newBody);
			state.currentId = newBody.id;
		},
		removeTweetBody: (state, action) => {
			const { id } = action.payload;

			state.tweetBodies = state.tweetBodies.filter(
				(body) => body.id !== id
			);

			state.currentId =
				state.tweetBodies[state.tweetBodies.length - 1].id;
		},
		updateTweetBodyLength: (state, action) => {
			const { id, charCount } = action.payload;
			const tweetBody = state.tweetBodies.find((body) => body.id === id);
			if (tweetBody) {
				tweetBody.charCount = charCount;
			}
			state.currentId = id;
		},
		changeGroup: (state, action) => {
			state.group = action.payload;
		},
	},
});

export const {
	addTweetBody,
	removeTweetBody,
	updateTweetBodyLength,
	changeGroup,
} = createTweetSlice.actions;

export const selectTweetBodies = (state) => state.createTweet.tweetBodies;
export const selectNextId = (state) => state.createTweet.nextId;

export default createTweetSlice.reducer;