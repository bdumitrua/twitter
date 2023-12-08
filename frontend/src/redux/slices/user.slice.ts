import { UserState } from "@/types/redux/user";
import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import UserService from "../services/user.service";

export const getMeAsync = createAsyncThunk("user/me", async () => {
	const user = await UserService.getMe();
	console.log(user);

	return user;
});

const initialState: UserState = {
	user: null,
	isSuccessfull: null,
	loading: false,
	error: null,
};

const userSlice = createSlice({
	name: "user",
	initialState,
	reducers: {
		resetUser: (state) => {
			state.user = null;
			state.isSuccessfull = null;
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
				state.error = action.error.message || "Неизвестная ошибка";
			});
	},
});

export const { resetUser } = userSlice.actions;
export default userSlice.reducer;
