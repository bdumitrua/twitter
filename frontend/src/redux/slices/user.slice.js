import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import UserService from "../services/user.service";

export const getMeAsync = createAsyncThunk("user/me", async () => {
	const user = await UserService.getMe();

	return user;
});

const initialState = {
	user: null,
	isSuccesfull: null,
	loading: false,
	error: null,
};

const userSlice = createSlice({
	name: "user",
	initialState,
	reducers: {
		resetUser: (state) => {
			state.user = null;
			state.isSuccesfull = null;
			state.loading = false;
			state.error = null;
		},
	},
	extraReducers: (builder) => {
		builder
			.addCase(getMeAsync.pending, (state) => {
				state.loading = true;
				state.error = null;
			})
			.addCase(getMeAsync.fulfilled, (state, action) => {
				state.loading = false;
				state.user = action.payload;
				state.error = null;
			})
			.addCase(getMeAsync.rejected, (state, action) => {
				state.loading = false;
				state.error = action.error.message;
			});
	},
});

export const { resetUser } = userSlice.actions;
export default userSlice.reducer;
