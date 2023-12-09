import {
	AddTweetBodyPayload,
	CreateTweetState,
	RemoveTweetBodyPayload,
	UpdateTweetBodyLengthPayload,
} from "@/types/redux/createTweet";
import { PayloadAction, createSlice } from "@reduxjs/toolkit";
import { RootState } from "../store";

const groupsList = ["Group 1", "Group 2", "Group 3"];

const initialState: CreateTweetState = {
	tweetBodies: [],
	groupsList: groupsList,
	group: null,
	currentId: 0,
};

const createTweetSlice = createSlice({
	name: "createTweet",
	initialState,
	reducers: {
		addTweetBody: (state, action: PayloadAction<AddTweetBodyPayload>) => {
			const newBody = {
				id: state.currentId + 1,
				...action.payload,
				charCount: 0,
			};
			state.tweetBodies.push(newBody);
			state.currentId = newBody.id;
		},
		removeTweetBody: (
			state,
			action: PayloadAction<RemoveTweetBodyPayload>
		) => {
			const { id } = action.payload;

			state.tweetBodies = state.tweetBodies.filter(
				(body) => body.id !== id
			);

			state.currentId =
				state.tweetBodies[state.tweetBodies.length - 1].id;
		},
		updateTweetBodyLength: (
			state,
			action: PayloadAction<UpdateTweetBodyLengthPayload>
		) => {
			const { id, charCount } = action.payload;
			const tweetBody = state.tweetBodies.find((body) => body.id === id);
			if (tweetBody) {
				tweetBody.charCount = charCount;
			}
			state.currentId = id;
		},
		changeGroup: (state, action: PayloadAction<string | null>) => {
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

export const selectTweetBodies = (state: RootState) =>
	state.createTweet.tweetBodies;

export default createTweetSlice.reducer;
