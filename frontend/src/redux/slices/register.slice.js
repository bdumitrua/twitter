import { createAsyncThunk, createSlice } from "@reduxjs/toolkit";
import { getLastEntry } from "../../utils/functions/getLastEntry";
import RegisterService from "../services/register.service";

export const startRegisterAsync = createAsyncThunk(
	"register/start",
	async ({ name, email, birth_date }) => {
		try {
			const registrationId = await RegisterService.registerStart(
				name,
				email,
				birth_date
			);
			return registrationId;
		} catch (error) {
			console.error(error);
			throw error;
		}
	}
);

export const codeRegisterAsync = createAsyncThunk(
	"register/code",
	async ({ code, registrationId }) => {
		try {
			const status = await RegisterService.registerCode(
				code,
				registrationId
			);
			return status;
		} catch (error) {
			console.error(error);
			throw error;
		}
	}
);

export const registerAsync = createAsyncThunk(
	"register/signup",
	async ({ password, registrationId }) => {
		try {
			const status = await RegisterService.register(
				password,
				registrationId
			);
			return status;
		} catch (error) {
			console.error(error);
			throw error;
		}
	}
);

const initialState = {
	registrationId: null,
	isSuccesfullCode: null,
	isSuccesfullRegistration: null,
	loading: false,
	error: null,
};

const handlePending = (state) => {
	state.loading = true;
	state.error = null;
};

const handleRejected = (state, action) => {
	state.loading = false;
	if (action.error) {
		state.error = {
			message: action.error.message,
			status: getLastEntry(action.error.message, " "),
		};
	}
};

const registerSlice = createSlice({
	name: "register",
	initialState,
	reducers: {},
	extraReducers: (builder) => {
		builder
			.addCase(startRegisterAsync.pending, handlePending)
			.addCase(startRegisterAsync.fulfilled, (state, action) => {
				state.loading = false;
				state.error = null;
				state.registrationId = action.payload;
			})
			.addCase(startRegisterAsync.rejected, handleRejected)

			.addCase(codeRegisterAsync.pending, handlePending)
			.addCase(codeRegisterAsync.fulfilled, (state, action) => {
				state.loading = false;
				state.error = null;
				state.isSuccesfullCode = action.payload;
			})
			.addCase(codeRegisterAsync.rejected, handleRejected)

			.addCase(registerAsync.pending, handlePending)
			.addCase(registerAsync.fulfilled, (state, action) => {
				state.loading = false;
				state.error = null;
				state.isSuccesfullRegistration = action.payload;
			})
			.addCase(registerAsync.rejected, handleRejected);
	},
});

export default registerSlice.reducer;
